<?php 

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Mockery;
use App\Model\CompositionRequest;
use App\Model\Review;
use App\Model\User;
use App\Notifications\CompositionRequestAcceptNotification;
use App\Notifications\CompositionRequestRejectNotification;

class ReviewStatusUpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        Storage::fake();
    }

    public function testValidationFails()
    {
        $user = Mockery::mock(\App\Model\User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('client_id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('password_changed_at')->andReturn('2025-01-01 00:00:00');
        $user->shouldReceive('getAttribute')->with('projects')->andReturn((object) ['projects' => collect([(object) ['id' => 100, 'code' => 'IATA']])]);
        $user->shouldReceive('hasPermission')->andReturn(true);

        $this->actingAs($user);

        $request = Request::create('/update-status', 'POST', []);
        
        $response = $this->patchJson(route('reviews.post-update-status', ['prj_code' => 'IATA', 'order_id' => 1, 'request_id' => 1, 'review_id' => 1]), []);
        
        $response->assertStatus(200);
        $response->assertJson([
            'status' => false,
            'msg' => 'The review status field is required.<br>The Notify Users field is required.',
        ]);
    }

    public function testInvalidProject()
    {
        $user = \App\Model\User::inRandomOrder()->first();

        $this->actingAs($user);

        $request = Request::create(route('reviews.post-update-status', ['prj_code' => 'invalid_project', 'order_id' => 1, 'request_id' => 1, 'review_id' => 1]), 'POST', ['review_status' => 'A', 'notify-users' => [2, 3]]);
        
        $response = $this->patchJson(route('reviews.post-update-status', ['prj_code' => 'invalid_project', 'order_id' => 1, 'request_id' => 1, 'review_id' => 1]), $request->all());

        $response->assertStatus(404);
        // $response->assertJson(['status' => false, 'msg' => 'Invalid request.']);
    }

    public function testInvalidCompositionRequest()
    {
        $userProject = DB::table('user_projects')->inRandomOrder()->first();
        $this->assertNotNull($userProject, 'No user_project found in the database.');

        $user = \App\Model\User::find($userProject->user_id);
        $project = \App\Model\Project::find($userProject->project_id);
        $prj_code = $project->code;

        // Act as the created user
        $this->actingAs($user);

        $response = $this->patchJson(route('reviews.post-update-status', ['prj_code' => $prj_code, 'order_id' => 1, 'request_id' => 'invalid_id', 'review_id' => 1]), [
            'review_status' => 'A',
            'notify-users' => [2, 3]
        ]);

        $response->assertStatus(404);
        // $response->assertJson(['status' => false, 'msg' => 'Invalid request.']);
    }

    public function testUnauthorizedAccess()
    {
        $userProject = DB::table('user_projects')->inRandomOrder()->first();
        $this->assertNotNull($userProject, 'No user_project found in the database.');

        $user = \App\Model\User::find($userProject->user_id);
        $project = \App\Model\Project::find($userProject->project_id);
        $prj_code = $project->code;

        // Mock the hasTitlePermission method to return false
        $userMock = Mockery::mock($user);
        $userMock->shouldReceive('hasTitlePermission')->with('accept-reject-review.review', Mockery::any())->andReturn(false);
        
        $this->actingAs($userMock);

        $response = $this->patchJson(route('reviews.post-update-status', ['prj_code' => $prj_code, 'order_id' => 1, 'request_id' => 1, 'review_id' => 1]), [
            'review_status' => 'A',
            'notify-users' => [2, 3]
        ]);
        // dump($response->getContent());
        $response->assertStatus(200);
        $response->assertJson(['status' => false, 'msg' => 'Invalid request.', 'error' => 'Invalid request.']);
    }
}
