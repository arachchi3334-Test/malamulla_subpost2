<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0">
	<xsl:output indent="no" method="xml" encoding="utf-8"/>
	<xsl:include href="_table_CALS2HTML.xsl"/>
	
	<xsl:template match="*">
		<!--<xsl:element name="{name()}">-->
		<span>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</span>
		<!--</xsl:element>-->
	</xsl:template>
	
	<xsl:template match="processing-instruction()">
		<xsl:variable name="PIName" select="name(.)"/>
		<xsl:variable name="PIcontent">
			<xsl:value-of select="."/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="$PIName='annotation'">
				<xsl:text disable-output-escaping="yes">&lt;annotation </xsl:text>
				<xsl:value-of select="substring-before($PIcontent, ' data-text')"/>
				<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
				<xsl:value-of select="substring-before(substring-after($PIcontent, ' data-text=&quot;'), '&quot;')"/>
				<xsl:text disable-output-escaping="yes">&lt;/annotation&gt;</xsl:text>
			</xsl:when>
			<xsl:when test="$PIName='ins'">
				<xsl:text disable-output-escaping="yes">&lt;ins </xsl:text>
				<xsl:value-of select="substring-before($PIcontent, ' data-text')"/>
				<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
				<xsl:value-of select="substring-before(substring-after($PIcontent, ' data-text=&quot;'), '&quot;')"/>
				<xsl:text disable-output-escaping="yes">&lt;/ins&gt;</xsl:text>
			</xsl:when>
			<xsl:when test="$PIName='del'">
				<xsl:text disable-output-escaping="yes">&lt;del </xsl:text>
				<xsl:value-of select="substring-before($PIcontent, ' data-text')"/>
				<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
				<xsl:value-of select="substring-before(substring-after($PIcontent, ' data-text=&quot;'), '&quot;')"/>
				<xsl:text disable-output-escaping="yes">&lt;/del&gt;</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:copy/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template> 

	<xsl:template match="comment()">
		<xsl:copy/>
	</xsl:template>

	<xsl:template match="/">
		<root>
			<xsl:apply-templates/>
			<section class="footnotes">
				<header>
					<h2>Footnotes</h2>
				</header>
				<ol>
				<xsl:for-each select="//foot-note">
					
						<li>
							<xsl:attribute name="data-footnote-id">
								<xsl:value-of select="@id"/>
							</xsl:attribute>
							<xsl:attribute name="id">
								<xsl:text>footnote-</xsl:text>
								<xsl:value-of select="@callout"/>
							</xsl:attribute>
							<sup>
								<a>
									<xsl:attribute name="href">
										<xsl:text>#footnote-marker-</xsl:text>
										<xsl:value-of select="@callout"/>
										<xsl:text>-</xsl:text>
										<xsl:value-of select="@callout"/>
									</xsl:attribute>
									<xsl:text>^</xsl:text>
								</a> 
							</sup>
							<cite>
								<xsl:choose>
								<xsl:when test="child::FootNoteBody">
								<xsl:copy>
									<xsl:apply-templates select="child::FootNoteBody/node()"/>
								</xsl:copy>
								</xsl:when>
								<xsl:when test="child::foot-noteBody">
								<xsl:copy>
									<xsl:apply-templates select="child::foot-noteBody/node()"/>
								</xsl:copy>
								</xsl:when>
								<xsl:otherwise><xsl:apply-templates select="./node()"/></xsl:otherwise>
								</xsl:choose>
								<!--<xsl:copy-of select="child::FootNoteBody/node()"></xsl:copy-of>-->
							</cite>
						</li>
					
				</xsl:for-each>
				</ol>
			</section>
		</root>
	</xsl:template>

    <xsl:template match="Section1|Section2|Section3|Section4|Section5|section">
    	<div>
    		<xsl:attribute name="data-element">
    			<xsl:value-of select="local-name()"/>
    		</xsl:attribute>
    		<xsl:copy-of select="@*"/>
    		<xsl:apply-templates/>
    	</div>
    </xsl:template>
	
	<xsl:template match="sub-level">
    	<div>
    		<xsl:attribute name="data-element">
    			<xsl:value-of select="local-name()"/>
    		</xsl:attribute>
    		<xsl:copy-of select="@*"/>
    		<xsl:apply-templates/>
    	</div>
    </xsl:template>
	
	<xsl:template match="Index|index">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="Para|para">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="text">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="Note|Notes|notes|note">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="Address|address">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="State|state">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="Information|information">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="AnimalSizes|animal-sizes">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="FigureHeading|figure-heading">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="ApplicableTo|applicable-to">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="ContainerRequirement|container-requirement">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="Example|example">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="Species">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="PartyName|name">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<xsl:template match="List|list">
		<ul>
			<xsl:attribute name="data-element">
				<xsl:text>list</xsl:text>
			</xsl:attribute>
			<xsl:attribute name="class">
				<xsl:value-of select="@type"/>
			</xsl:attribute>
			<xsl:apply-templates/>
		</ul>
	</xsl:template>

	<xsl:template match="ListItem|list-item">
		<li>
			<xsl:attribute name="data-element">
				<xsl:text>list-item</xsl:text>
			</xsl:attribute>
			<!-- <p class="ListItem">
				<xsl:attribute name="para">
				<xsl:text>list-item</xsl:text>
				</xsl:attribute> -->
				<xsl:apply-templates/>
			<!-- </p> -->
		</li>
	</xsl:template>

	<xsl:template match="TableGroup|table-group">
		<div>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
<!--	<xsl:template match="Table">
		<table>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</table>
	</xsl:template>

	<xsl:template match="row">
		<tr>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</tr>
	</xsl:template>

	<xsl:template match="entry">
		<td>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</td>
	</xsl:template>-->

	<xsl:template match="XRef|xref">
		<a>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</a>
	</xsl:template>
	
	<xsl:template match="Emphasis|emphasis">
		<xsl:choose>
			<xsl:when test="@style='sups'">
				<span>
					<xsl:attribute name="data-element">
						<xsl:text>emphasis</xsl:text>
					</xsl:attribute>
					<xsl:attribute name="data-style">
						<xsl:text>sups</xsl:text>
					</xsl:attribute>
					<xsl:apply-templates/>
				</span>
			</xsl:when>
			<xsl:when test="@style='subs'">
				<span>
					<xsl:attribute name="data-element">
						<xsl:text>emphasis</xsl:text>
					</xsl:attribute>
					<xsl:attribute name="data-style">
						<xsl:text>subs</xsl:text>
					</xsl:attribute>
					<xsl:apply-templates/>
				</span>
			</xsl:when>
			<xsl:when test="@style='bold'">
				<span>
					<xsl:attribute name="data-style">
						<xsl:text>bold</xsl:text>
					</xsl:attribute>
					<xsl:attribute name="data-element">
						<xsl:text>emphasis</xsl:text>
					</xsl:attribute>
					<xsl:apply-templates/>
				</span>
			</xsl:when>
			<xsl:when test="@style='italic'">
				<span>
					<xsl:attribute name="data-style">
						<xsl:text>italic</xsl:text>
					</xsl:attribute>
					<xsl:attribute name="data-element">
						<xsl:text>emphasis</xsl:text>
					</xsl:attribute>
					<xsl:apply-templates/>
				</span>
			</xsl:when>
			<xsl:when test="@style='underline'">
				<span>
					<xsl:attribute name="data-style">
						<xsl:text>underline</xsl:text>
					</xsl:attribute>
					<xsl:attribute name="data-element">
						<xsl:text>emphasis</xsl:text>
					</xsl:attribute>
					<xsl:apply-templates/>
				</span>
			</xsl:when>
			<xsl:when test="@style='strikethrough'">
				<span>
					<xsl:attribute name="data-style">
						<xsl:text>strikethrough</xsl:text>
					</xsl:attribute>
					<xsl:attribute name="data-element">
						<xsl:text>emphasis</xsl:text>
					</xsl:attribute>
					<xsl:apply-templates/>
				</span>
			</xsl:when>
			<xsl:when test="@style='large'">
				<span>
					<xsl:attribute name="style">
						<xsl:text>font-weight: large;</xsl:text>
					</xsl:attribute>
					<xsl:attribute name="data-element">
						<xsl:text>emphasis</xsl:text>
					</xsl:attribute>
					<xsl:apply-templates/>
				</span>
			</xsl:when>
			<xsl:when test="@style='foregroundcolor'">
				<span>
					<xsl:attribute name="style">
						<xsl:text>color:</xsl:text>
						<xsl:value-of select="@foregroundcolor"/>
					</xsl:attribute>
					<xsl:attribute name="data-element">
						<xsl:text>emphasis</xsl:text>
					</xsl:attribute>
					<xsl:apply-templates/>
				</span>
			</xsl:when>
			<xsl:when test="starts-with(@style, 'decorative_text')">
				<span>
					<xsl:attribute name="data-style">
						<xsl:value-of select="@style"/>
					</xsl:attribute>
					<xsl:attribute name="data-element">
						<xsl:text>emphasis</xsl:text>
					</xsl:attribute>
					<xsl:apply-templates/>
				</span>
			</xsl:when>
			<xsl:otherwise>
				<check>
					<xsl:apply-templates/>
				</check>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="Break|break">
		<br/>
	</xsl:template>
	

	
	<xsl:template match="FootNote|foot-note">
		<sup>
			<xsl:attribute name="data-footnote-id">
				<xsl:value-of select="@id"/>
			</xsl:attribute>
			<xsl:attribute name="data-element">
						<xsl:text>foot-note</xsl:text>
			</xsl:attribute>
			<a href="#footnote-1" id="footnote-marker-1-1" rel="footnote">
				<xsl:attribute name="href">
					<xsl:text>#footnote-</xsl:text>
					<xsl:value-of select="@callout"/>
				</xsl:attribute>	
				<xsl:attribute name="id">
						<xsl:text>footnote-marker-</xsl:text>
						<xsl:value-of select="@callout"/>
						<xsl:text>-</xsl:text>
						<xsl:value-of select="@callout"/>
				</xsl:attribute>
				<xsl:attribute name="rel">
					<xsl:text>footnote</xsl:text>
				</xsl:attribute>
				<xsl:text>[</xsl:text>
				<xsl:value-of select="@callout"/>				
				<xsl:text>]</xsl:text>
			</a>
		</sup>
	</xsl:template>
	
	<xsl:template match="media-object">
		<img>
			<xsl:attribute name="data-element">
						<xsl:text>media-object</xsl:text>
					</xsl:attribute>
			<xsl:attribute name="src">
				<xsl:value-of select="@lsrc"/>
			</xsl:attribute>
			<xsl:attribute name="data-width">
				<xsl:value-of select="@width"/>
			</xsl:attribute>
			<xsl:attribute name="data-height">
				<xsl:value-of select="@height"/>
			</xsl:attribute>
			<xsl:attribute name="data-hsrc">
				<xsl:value-of select="@hsrc"/>
			</xsl:attribute>
		</img>
	</xsl:template>
	
	<xsl:template match="xi_include">
		<a>
			<xsl:attribute name="data-element">
				<xsl:text>xi_include</xsl:text>
			</xsl:attribute>
			<xsl:attribute name="href">
				<xsl:value-of select="@href"/>
			</xsl:attribute>
			<xsl:attribute name="fragid">
				<xsl:value-of select="@fragid"/>
			</xsl:attribute>
			<xsl:value-of select="@href"/>
		</a>
	</xsl:template>
	
	
</xsl:stylesheet><!-- Stylus Studio meta-information - (c) 2004-2006. Progress Software Corporation. All rights reserved.
<metaInformation>
<scenarios ><scenario default="yes" name="Scenario1" userelativepaths="yes" externalpreview="no" url="..\01_IN\lar&#x2D;12&#x2D;en.xml" htmlbaseurl="" outputurl="..\01_IN\lar&#x2D;12&#x2D;en_XML2html.xml" processortype="saxon8" useresolver="yes" profilemode="0" profiledepth="" profilelength="" urlprofilexml="" commandline="" additionalpath="" additionalclasspath="" postprocessortype="none" postprocesscommandline="" postprocessadditionalpath="" postprocessgeneratedext="" validateoutput="no" validator="internal" customvalidator=""/></scenarios><MapperMetaTag><MapperInfo srcSchemaPathIsRelative="yes" srcSchemaInterpretAsXML="no" destSchemaPath="" destSchemaRoot="" destSchemaPathIsRelative="yes" destSchemaInterpretAsXML="no"/><MapperBlockPosition></MapperBlockPosition><TemplateContext></TemplateContext><MapperFilter side="source"></MapperFilter></MapperMetaTag>
</metaInformation>
-->