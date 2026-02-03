<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0">
	<xsl:output indent="no" method="xml" encoding="utf-8"/>
	<xsl:include href="_table_HTML2CALS.xsl"/>
	<xsl:template match="*">
		<xsl:element name="{name()}">
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>

	<xsl:template match="annotation|ins|del">
		<!--<annotation>
			<xsl:attribute name="data-element">
				<xsl:value-of select="local-name()"/>
			</xsl:attribute>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</annotation>-->
		<xsl:processing-instruction name="annotation">
			<xsl:for-each select="@*">
				<xsl:value-of select="name()"/>
				<xsl:text>="</xsl:text>
				<xsl:value-of select="current()"/>
				<xsl:text>"</xsl:text>
				<xsl:text xml:space="preserve"> </xsl:text>
			</xsl:for-each>
			<xsl:text>data-text="</xsl:text>
			<xsl:apply-templates/>
			<xsl:text>"</xsl:text>
		</xsl:processing-instruction>
	</xsl:template>
	
	<xsl:template match="processing-instruction()">
		<xsl:copy/>
	</xsl:template>

	<xsl:template match="comment()">
		<xsl:copy/>
	</xsl:template> 

	<xsl:template match="FileName"/>

	<xsl:template match="root">
		<xsl:apply-templates/>
	</xsl:template>

	<xsl:template match="div">
		<xsl:variable name="eleName" select="@data-element"/>
		<xsl:choose>
			<xsl:when test="$eleName='remove'">
				<xsl:apply-templates/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:element name="{$eleName}">
					<xsl:for-each select="@*">
						<xsl:variable name="attName" select="name(.)"/>
						<xsl:choose>
							<xsl:when test="$attName!='data-element'">
								<xsl:attribute name="{$attName}">
									<xsl:value-of select="."/>
								</xsl:attribute>
							</xsl:when>
							<xsl:otherwise></xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
					<!--<xsl:copy-of select="@*[not(data-element)]"/>-->
					<xsl:apply-templates/>
				</xsl:element>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="span">
		<xsl:variable name="eleName" select="@data-element"/>
		<xsl:choose>
			<xsl:when test="$eleName='remove'">
				<xsl:apply-templates/>
			</xsl:when>
			<xsl:when test="$eleName='emphasis'">
				<xsl:variable name="styleVal" select="@data-style"/>
				<xsl:choose>
					<xsl:when test="$styleVal='bold'">
						<emphasis style="bold">
							<xsl:apply-templates/>
						</emphasis>
					</xsl:when>
					<xsl:when test="$styleVal='italic'">
						<emphasis style="italic">
							<xsl:apply-templates/>
						</emphasis>
					</xsl:when>
					<xsl:when test="$styleVal='underline'">
						<emphasis style="underline">
							<xsl:apply-templates/>
						</emphasis>
					</xsl:when>
					<xsl:when test="$styleVal='strikethrough'">
						<emphasis style="strikethrough">
							<xsl:apply-templates/>
						</emphasis>
					</xsl:when>
					<xsl:when test="$styleVal='font-weight: large;'">
						<emphasis style="large">
							<xsl:apply-templates/>
						</emphasis>
					</xsl:when>
					<xsl:when test="starts-with($styleVal, 'color:')">
						<emphasis>
							<xsl:attribute name="foregroundcolor">
								<xsl:value-of select="substring-after($styleVal, 'color:')"/>
							</xsl:attribute>
							<xsl:apply-templates/>
						</emphasis>
					</xsl:when>
					<xsl:when test="starts-with($styleVal, 'decorative_text')">
						<emphasis>
							<xsl:attribute name="style">
								<xsl:value-of select="$styleVal"/>
							</xsl:attribute>
							<xsl:apply-templates/>
						</emphasis>
					</xsl:when>
					<xsl:when test="$styleVal=''">
						<emphasis>
							<xsl:apply-templates/>
						</emphasis>
					</xsl:when>
				</xsl:choose>
				<!--<xsl:apply-templates/>-->
			</xsl:when>
			<xsl:otherwise>
				<xsl:element name="{$eleName}">
					<xsl:for-each select="@*">
						<xsl:variable name="attName" select="name(.)"/>
						<xsl:choose>
							<xsl:when test="$attName='data-element'"/>
							<xsl:when test="$attName='data-style'"/>
							<xsl:otherwise>
								<xsl:attribute name="{$attName}">
									<xsl:value-of select="."/>
								</xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
					<!--<xsl:copy-of select="@*[not(data-element)]"/>-->
					<xsl:apply-templates/>
				</xsl:element>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="ul">
		<list>
			<xsl:for-each select="@*">
				<xsl:variable name="attName" select="name(.)"/>
				<xsl:choose>
					<xsl:when test="$attName='data-element'">
					</xsl:when>
					<xsl:when test="$attName='class'">
						<xsl:attribute name="type">
							<xsl:value-of select="."/>
						</xsl:attribute>
					</xsl:when>
					<xsl:otherwise>
						<xsl:attribute name="{$attName}">
							<xsl:value-of select="."/>
						</xsl:attribute>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
			<!-- <xsl:attribute name="type">
				<xsl:value-of select="@class"/>
			</xsl:attribute> -->
			<xsl:apply-templates/>
		</list>
	</xsl:template>

	<xsl:template match="li">
		<!-- 		<xsl:choose>
			<xsl:when test="ancestor::li">
				<xsl:choose>
					<xsl:when test="child::p[@class]">
						<xsl:apply-templates/>
					</xsl:when>
					<xsl:otherwise>
						<ListSubItem>
							<xsl:apply-templates/>
						</ListSubItem>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise> -->
		<list-item>
			<xsl:apply-templates/>
		</list-item>
		<!-- </xsl:otherwise>
		</xsl:choose> -->
	</xsl:template>

	<!--<xsl:template match="table">
		<TableGroup>
			<Table>
				<xsl:copy-of select="@*"/>
				<xsl:apply-templates/>
			</Table>
			<xsl:if test="child::caption">
				<TableCaption>
					<xsl:value-of select="child::caption"/>
				</TableCaption>
			</xsl:if>
			<xsl:if test=".//sup[@data-footnote-id]">
				<TableFootNotes>
					<xsl:for-each select=".//sup[@data-footnote-id]">
						<xsl:variable name="sectionIDTab" select="preceding::span[@class='SectionNumber'][1]/text()"/>
						<xsl:variable name="FcountTextTab" select="a/@href"/>
						<xsl:variable name="FcountTab" select="substring-after($FcountTextTab,'-')"/>
						<xsl:variable name="FIDTab" select="./attribute::data-footnote-id"/>
						<xsl:if test="//section[@class='footnotes']//li[@data-footnote-id=$FIDTab]">
							<TableFootNote>
								<xsl:attribute name="id">
									<xsl:value-of select="$sectionIDTab"/>
									<xsl:text>-FN</xsl:text>
									<xsl:value-of select="$FcountTab"/>
								</xsl:attribute>
								<xsl:attribute name="callout">
									<xsl:value-of select="$FcountTab"/>
								</xsl:attribute>
								<xsl:copy-of select="//section[@class='footnotes']//li[@data-footnote-id=$FIDTab]//cite"/>
							</TableFootNote>
						</xsl:if>
					</xsl:for-each>
				</TableFootNotes>
			</xsl:if>
		</TableGroup>
	</xsl:template>-->

	<xsl:template match="section[@class='footnotes']"/>

	<!--<xsl:template match="ins">
		<ins>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</ins>
	</xsl:template>

	<xsl:template match="del">
		<del>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</del>
	</xsl:template>

	<xsl:template match="annotation">
		<annotation>
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</annotation>
	</xsl:template>-->

	<xsl:template match="a">
		<xsl:choose>
			<xsl:when test="@rel='footnote'">
				<xsl:if test="ancestor::table">
					<footref>
						<xsl:attribute name="href">
							<xsl:variable name="sectionIDTabRef" select="preceding::span[@class='SectionNumber'][1]/text()"/>
							<xsl:variable name="FcountTextTabRef" select="./@href"/>
							<xsl:variable name="FcountTabRef" select="substring-after($FcountTextTabRef,'-')"/>
							<xsl:value-of select="$sectionIDTabRef"/>
							<xsl:text>-FN</xsl:text>
							<xsl:value-of select="$FcountTabRef"/>
						</xsl:attribute>
					</footref>
				</xsl:if>
			</xsl:when>
			<xsl:when test="@data-element='xi_include'">
				<xi_include>
					<xsl:attribute name="href">
						<xsl:value-of select="@href"/>
					</xsl:attribute>
					<xsl:if test="@fragid">
						<xsl:value-of select="@fragid"/>
					</xsl:if>
					<xsl:attribute name="parse">
						<xsl:text>xml</xsl:text>
					</xsl:attribute>
				</xi_include>
			</xsl:when>
			<xsl:otherwise>
				<xref>
					<xsl:for-each select="@*">
						<xsl:variable name="attName" select="name(.)"/>
						<xsl:choose>
							<xsl:when test="$attName='data-element'">
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="{$attName}">
									<xsl:value-of select="."/>
								</xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
					<!-- <xsl:copy-of select="@*"/> -->
					<xsl:apply-templates/>
				</xref>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="img">
		<xsl:choose>
			<xsl:when test="ancestor::figure[@class='image']">
				<figure-body>
					<!--<media-object>
						<xsl:for-each select="@*">
							<xsl:variable name="attName" select="name(.)"/>
							<xsl:choose>
								<xsl:when test="$attName='data-element'">
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="{$attName}">
										<xsl:value-of select="."/>
									</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
						<xsl:apply-templates/>
					</media-object>-->
					<media-object>
						<xsl:attribute name="lsrc">
							<xsl:value-of select="@src"/>
						</xsl:attribute>
						<xsl:attribute name="width">
							<xsl:value-of select="@data-width"/>
						</xsl:attribute>
						<xsl:attribute name="height">
							<xsl:value-of select="@data-height"/>
						</xsl:attribute>
						<xsl:attribute name="hsrc">
							<xsl:value-of select="@data-hsrc"/>
						</xsl:attribute>
					</media-object>
				</figure-body>
			</xsl:when>
			<xsl:otherwise>
				<media-object>
					<xsl:for-each select="@*">
						<xsl:variable name="attName" select="name(.)"/>
						<xsl:choose>
							<xsl:when test="$attName='data-element'">
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="{$attName}">
									<xsl:value-of select="."/>
								</xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
					<xsl:apply-templates/>
				</media-object>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="figure">
		<figure>
			<xsl:apply-templates/>
		</figure>
	</xsl:template>

	<xsl:template match="figcaption">
		<figure-caption>
			<xsl:apply-templates/>
		</figure-caption>
	</xsl:template>

	<xsl:template match="sup[not(ancestor::table)]" name="Footnote">
		<xsl:variable name="sectionID" select="preceding::span[@class='SectionNumber'][1]/text()"/>
		<xsl:choose>
			<xsl:when test="@data-footnote-id">
				<xsl:variable name="FcountText" select="a/@href"/>
				<xsl:variable name="Fcount" select="substring-after($FcountText,'-')"/>
				<xsl:variable name="FID" select="./attribute::data-footnote-id"/>
				<xsl:if test="//section[@class='footnotes']//li[@data-footnote-id=$FID]">
					<foot-note>
						<xsl:attribute name="id">
							<!--<xsl:value-of select="$sectionID"/>
							<xsl:text>-FN</xsl:text>
							<xsl:value-of select="$Fcount"/>-->
							<xsl:value-of select="$FID"/>
						</xsl:attribute>
						<xsl:attribute name="callout">
							<xsl:value-of select="$Fcount"/>
						</xsl:attribute>
						<!-- <FootNoteBody> -->
						<xsl:copy-of select="//section[@class='footnotes']//li[@data-footnote-id=$FID]//cite">
						</xsl:copy-of>
						<!-- </FootNoteBody> -->
					</foot-note>
				</xsl:if>
			</xsl:when>
			<xsl:otherwise>
				<emphasis style="sups">
					<xsl:apply-templates/>
				</emphasis>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	

	<xsl:template match="br">
		<break/>
	</xsl:template>

	<!--<xsl:template match="img">
		<media-object>
			<xsl:attribute name="lsrc">
				<xsl:value-of select="@src"/>
			</xsl:attribute>
			<xsl:attribute name="width">
				<xsl:value-of select="@data-width"/>
			</xsl:attribute>
			<xsl:attribute name="height">
				<xsl:value-of select="@data-height"/>
			</xsl:attribute>
			<xsl:attribute name="hsrc">
				<xsl:value-of select="@data-hsrc"/>
			</xsl:attribute>
		</media-object>
	</xsl:template>-->

</xsl:stylesheet><!-- Stylus Studio meta-information - (c) 2004-2006. Progress Software Corporation. All rights reserved.
<metaInformation>
<scenarios ><scenario default="yes" name="Scenario1" userelativepaths="yes" externalpreview="no" url="..\01_IN\lar&#x2D;12&#x2D;en_XML2html.xml" htmlbaseurl="" outputurl="..\01_IN\lar&#x2D;12&#x2D;en_html2xml.xml" processortype="saxon8" useresolver="yes" profilemode="0" profiledepth="" profilelength="" urlprofilexml="" commandline="" additionalpath="" additionalclasspath="" postprocessortype="none" postprocesscommandline="" postprocessadditionalpath="" postprocessgeneratedext="" validateoutput="no" validator="internal" customvalidator=""/></scenarios><MapperMetaTag><MapperInfo srcSchemaPathIsRelative="yes" srcSchemaInterpretAsXML="no" destSchemaPath="" destSchemaRoot="" destSchemaPathIsRelative="yes" destSchemaInterpretAsXML="no"/><MapperBlockPosition></MapperBlockPosition><TemplateContext></TemplateContext><MapperFilter side="source"></MapperFilter></MapperMetaTag>
</metaInformation>
-->