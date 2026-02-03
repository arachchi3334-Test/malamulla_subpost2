<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0">
	<xsl:output indent="no" method="xml" encoding="utf-8"/>

	<xsl:template match="*">
		<xsl:element name="{name()}">
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>

	<xsl:template match="Table">
		<table>
			<xsl:attribute name="data-element">
						<xsl:text>Table</xsl:text>
					</xsl:attribute>
			<xsl:if test="@id">
				<xsl:attribute name="id">
					<xsl:value-of select="@id"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@frame">
				<xsl:attribute name="frame">
					<xsl:choose>
						<xsl:when test="@frame='sides'">vsides</xsl:when>
						<xsl:when test="@frame='top'">above</xsl:when>
						<xsl:when test="@frame='bottom'">below</xsl:when>
						<xsl:when test="@frame='topbot'">hsides</xsl:when>
						<xsl:when test="@frame='all'">box</xsl:when>
						<xsl:when test="@frame='none'"></xsl:when>
						<xsl:otherwise>box</xsl:otherwise>	<!-- default -->
					</xsl:choose>				
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@bgshade">
				<xsl:attribute name="data-bgshade">
					<xsl:value-of select="@bgshade"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@orient">
				<xsl:attribute name="data-orient">
					<xsl:value-of select="@orient"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@style">
				<xsl:attribute name="data-style">
					<xsl:value-of select="@style"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@pagewide">
				<xsl:attribute name="data-pagewide">
					<xsl:value-of select="@pagewide"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:apply-templates select="tgroup"/>
		</table>
	</xsl:template>
	
	<xsl:template match="tgroup">
		<!--<xsl:variable name="total-percents-colwidth"><xsl:call-template name="total-width"/></xsl:variable>-->
		<colgroup>
			<xsl:attribute name="data-element">
						<xsl:text>tgroup</xsl:text>
					</xsl:attribute>
			<xsl:if test="@cols">
				<xsl:attribute name="data-cols">
					<xsl:value-of select="@cols"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@align">
				<xsl:attribute name="data-valign">
					<xsl:value-of select="@valign"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@colsep">
				<xsl:attribute name="data-colsep">
					<xsl:value-of select="@colsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@rowsep">
				<xsl:attribute name="data-rowsep">
					<xsl:value-of select="@rowsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:apply-templates select="colspec">
				<!--<xsl:with-param name="total-percents-colwidth" select="$total-percents-colwidth"/>-->
			</xsl:apply-templates>
		</colgroup>
		
		<xsl:apply-templates select="thead"/>
		<xsl:if test="ancestor::TableGroup/child::TableFootNotes">
			<tfoot>
				<xsl:for-each select="ancestor::TableGroup//TableFootNote">
					<tr>
						<td>
							<xsl:attribute name="data-element">
						<xsl:text>para</xsl:text>
					</xsl:attribute>
							<xsl:attribute name="colspan">
								<xsl:value-of select="ancestor::TableGroup//tgroup/@cols"/>
							</xsl:attribute>
							<xsl:attribute name="data-callout">
								<xsl:value-of select="@callout"/>
							</xsl:attribute>
							<xsl:attribute name="data-id">
								<xsl:value-of select="@id"/>
							</xsl:attribute>
							<a>
								<xsl:attribute name="id">
									<xsl:value-of select="@id"/>
								</xsl:attribute>
								<xsl:attribute name="name">
									<xsl:value-of select="@id"/>
								</xsl:attribute>
								<span class="footnote">
									<xsl:value-of select="@callout"/>
								</span>
							</a>
							<xsl:value-of select="current()"/>
						</td>
					</tr>
				</xsl:for-each>
			</tfoot>
		</xsl:if>
		<xsl:apply-templates select="tbody"/>
	</xsl:template>
	
	<xsl:template match="TableFootNotes"/>
	
	<!--<xsl:template name="total-width">
		<xsl:param name="percents"
			select="colspec[contains(@colwidth,'*')]/@colwidth"/>
		<xsl:param name="total" select="'0'"/>
		<xsl:choose>
			<xsl:when test="count($percents)&gt;1">
				<xsl:call-template name="total-width">
					<xsl:with-param name="percents" select="$percents[position()&gt;1]"/>
					<xsl:with-param name="total"
						select="number($total)+number(substring-before($percents[1],'*'))"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise><xsl:value-of
				select="number($total)+number(substring-before($percents[1],'*'))"/></xsl:otherwise>
		</xsl:choose>
	</xsl:template>-->
	
	<xsl:template match="colspec">
		<!--<xsl:param name="total-percents-colwidth" select="'1'"/>-->
		<col>
			<xsl:if test="@colwidth">
			<xsl:attribute name="width">
			<xsl:choose>
				<xsl:when test="contains(@colwidth,'pt')">
					<xsl:value-of select="substring-before(@colwidth,'pt')"/>
				</xsl:when>
				<xsl:when test="contains(@colwidth,'*')">
					<!--<xsl:value-of select="{100 * number(substring-before(@colwidth,'*')) divnumber($total-percents-colwidth)}%"/>-->
					<xsl:value-of select="substring-before(@colwidth,'*')"/><xsl:text>%</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="@colwidth"/>
				</xsl:otherwise>
			</xsl:choose>
			</xsl:attribute>
			</xsl:if>
			<xsl:if test="@colnum">
				<xsl:attribute name="data-colnum">
					<xsl:value-of select="@colnum"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@colname">
				<xsl:attribute name="data-colname">
					<xsl:value-of select="@colname"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@align">
				<xsl:attribute name="data-align">
					<xsl:value-of select="@align"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@valign">
				<xsl:attribute name="data-valign">
					<xsl:value-of select="@valign"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@colsep">
				<xsl:attribute name="data-colsep">
					<xsl:value-of select="@colsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@rowsep">
				<xsl:attribute name="data-rowsep">
					<xsl:value-of select="@rowsep"/>
				</xsl:attribute>
			</xsl:if>
		</col>
	</xsl:template>
	
	<xsl:template match="thead">
		<thead>
			<xsl:attribute name="data-element">
						<xsl:text>thead</xsl:text>
					</xsl:attribute>
			<xsl:apply-templates/>
		</thead>
	</xsl:template>
	
	<xsl:template match="tbody">
		<tbody>
			<xsl:attribute name="data-element">
						<xsl:text>tbody</xsl:text>
					</xsl:attribute>
			<xsl:apply-templates />
		</tbody>
	</xsl:template>
	
	<xsl:template match="row">
		<tr>
			<xsl:attribute name="data-element">
						<xsl:text>row</xsl:text>
					</xsl:attribute>
			<xsl:if test="@rowsep">
				<xsl:attribute name="data-rowsep">
					<xsl:value-of select="@rowsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@valign">
				<xsl:attribute name="data-valign">
					<xsl:value-of select="@valign"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@bgshade">
				<xsl:attribute name="data-bgshade">
					<xsl:value-of select="@bgshade"/>
				</xsl:attribute>
			</xsl:if>
			<!--<xsl:apply-templates select="entry">
				<!-\-<xsl:with-param name="up-rowsep">
					<xsl:choose>
						<xsl:when test="@rowsep"><xsl:value-of select="@rowsep"/></xsl:when>
						<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose>
				</xsl:with-param>-\->
				<xsl:with-param name="td" select="'th'"/>
			</xsl:apply-templates>-->
			<xsl:apply-templates/>
		</tr>
	</xsl:template>
	
	<xsl:template match="tbody//entry">
		<td>
			<xsl:attribute name="data-element">
						<xsl:text>entry</xsl:text>
					</xsl:attribute>
			<xsl:if test="@align">
				<xsl:attribute name="align">
					<xsl:value-of select="@align"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@rowsep">
				<xsl:attribute name="data-rowsep">
					<xsl:value-of select="@rowsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@colsep">
				<xsl:attribute name="data-colsep">
					<xsl:value-of select="@colsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:choose>
				<xsl:when test="@colname">
					<xsl:attribute name="data-colname">
						<xsl:value-of select="@colname"/>
					</xsl:attribute>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="@namest!=''">
							<xsl:attribute name="data-colname">
								<xsl:value-of select="@namest"/>
							</xsl:attribute>
						</xsl:when>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="@namest!='' and @nameend!=''">
				<xsl:variable name="namestVal" select="substring-after(@namest, 'col')"/>
				<xsl:variable name="nameendVal" select="substring-after(@nameend, 'col')"/>
				<xsl:attribute name="colspan"><xsl:value-of select="number($nameendVal) - number($namestVal)+1"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="@morerows">
				<xsl:attribute name="rowspan"><xsl:value-of select="number(@morerows)+1"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="@valign">
				<xsl:attribute name="data-valign">
					<xsl:value-of select="@valign"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:apply-templates/>
		</td>
	</xsl:template>
	
	<xsl:template match="thead//entry">
		<th>
			<xsl:attribute name="data-element">
						<xsl:text>entry</xsl:text>
					</xsl:attribute>
			<xsl:if test="@align">
				<xsl:attribute name="align">
					<xsl:value-of select="@align"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@rowsep">
				<xsl:attribute name="data-rowsep">
					<xsl:value-of select="@rowsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@colsep">
				<xsl:attribute name="data-colsep">
					<xsl:value-of select="@colsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:choose>
				<xsl:when test="@colname">
					<xsl:attribute name="data-colname">
						<xsl:value-of select="@colname"/>
					</xsl:attribute>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="@namest!=''">
							<xsl:attribute name="data-colname">
								<xsl:value-of select="@namest"/>
							</xsl:attribute>
						</xsl:when>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="@namest!='' and @nameend!=''">
				<xsl:variable name="namestVal" select="substring-after(@namest, 'col')"/>
				<xsl:variable name="nameendVal" select="substring-after(@nameend, 'col')"/>
				<xsl:attribute name="colspan"><xsl:value-of select="number($nameendVal) - number($namestVal)+1"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="@morerows">
				<xsl:attribute name="rowspan"><xsl:value-of select="number(@morerows)+1"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="@valign">
				<xsl:attribute name="data-valign">
					<xsl:value-of select="@valign"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:apply-templates/>
		</th>
	</xsl:template>
	
	<xsl:template match="TableHeading">
		<caption>
			<xsl:attribute name="data-element">
						<xsl:text>TableHeading</xsl:text>
					</xsl:attribute>
			<xsl:apply-templates/>
		</caption>
	</xsl:template>
	
	<xsl:template match="FootRef">
		<xsl:variable name="FoorRefID" select="@href"/>
		<a>
			<xsl:attribute name="href">
				<xsl:text>#</xsl:text>
				<xsl:value-of select="@href"/>
			</xsl:attribute>
			<span class="footnote">
				<xsl:for-each select="ancestor::TableGroup//TableFootNote">
				<xsl:if test="current()/attribute::id=$FoorRefID">
					<xsl:value-of select="current()/@callout"/>
				</xsl:if>
				</xsl:for-each>
			</span>
		</a>
	</xsl:template>
	
	
	<!--<xsl:template match="entry">
		<xsl:param name="td" select="'td'"/>
		<!-\-<xsl:param name="up-rowsep"/>-\->
		<!-\-<xsl:variable name="align">
			<xsl:choose>
				<xsl:when test="@align"><xsl:value-of select="@align"/></xsl:when>
				<xsl:when test="ancestor::tgroup[1]/colspec[position()]/@align"><xsl:value-of select="ancestor::tgroup[1]/colspec[position()]/@align"/></xsl:when>
				<xsl:when test="ancestor::tgroup[1]/@align"><xsl:value-of select="ancestor::tgroup[1]/@align"/></xsl:when>
				<xsl:otherwise></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="valign">
			<xsl:choose>
				<xsl:when test="@valign"><xsl:value-of select="@valign"/></xsl:when>
				<xsl:when test="row/@valign"><xsl:value-of select="row/@valign"/></xsl:when>
				<xsl:when test="parent::tbody/@valign"><xsl:value-of select="parent::tbody/@valign"/></xsl:when>
				<xsl:when test="parent::thead/@valign"><xsl:value-of select="parent::thead/@valign"/></xsl:when>
				<xsl:otherwise></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>-\->
		
		<xsl:element name="{$td}">
			<xsl:if test="@namest">
				<xsl:attribute name="colspan"><xsl:value-of select="number(@nameend)-number(@namest)+1"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="@morerows">
				<xsl:attribute name="rowspan"><xsl:value-of select="number(@morerows)+1"/></xsl:attribute>
			</xsl:if>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@rowsep='0'"></xsl:when>
					<xsl:when test="../following-sibling::row">
						<xsl:choose>
							<xsl:when test="@rowsep='1' or $up-rowsep='1'">b </xsl:when>
							<xsl:when test="ancestor::tgroup/colspec[position()]/@rowsep='1'">b</xsl:when>
							<xsl:when test="ancestor::tgroup/@rowsep='1'">b </xsl:when>
							<xsl:when test="ancestor::table/@rowsep='1'">b </xsl:when>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise></xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="@colsep='0'"></xsl:when>
					<xsl:when test="following-sibling::entry">
						<xsl:choose>
							<xsl:when test="@colsep='1'">r </xsl:when>
							<xsl:when test="ancestor::tgroup/colspec[position()]/@colsep='1'">r
							</xsl:when>
							<xsl:when test="ancestor::tgroup/@colsep='1'">r </xsl:when>
							<xsl:when test="ancestor::table/@colsep='1'">r </xsl:when>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise></xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:if test="$valign!=''">
				<xsl:attribute name="valign">
					<xsl:value-of select="$valign"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="$align!=''">
				<xsl:attribute name="align">
					<xsl:value-of select="$align"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>-->
</xsl:stylesheet><!-- Stylus Studio meta-information - (c) 2004-2006. Progress Software Corporation. All rights reserved.
<metaInformation>
<scenarios ><scenario default="yes" name="Scenario1" userelativepaths="yes" externalpreview="no" url="..\01_IN\lar&#x2D;12&#x2D;en.xml" htmlbaseurl="" outputurl="..\01_IN\lar&#x2D;12&#x2D;en_XML2html.xml" processortype="saxon8" useresolver="yes" profilemode="0" profiledepth="" profilelength="" urlprofilexml="" commandline="" additionalpath="" additionalclasspath="" postprocessortype="none" postprocesscommandline="" postprocessadditionalpath="" postprocessgeneratedext="" validateoutput="no" validator="internal" customvalidator=""/></scenarios><MapperMetaTag><MapperInfo srcSchemaPathIsRelative="yes" srcSchemaInterpretAsXML="no" destSchemaPath="" destSchemaRoot="" destSchemaPathIsRelative="yes" destSchemaInterpretAsXML="no"/><MapperBlockPosition></MapperBlockPosition><TemplateContext></TemplateContext><MapperFilter side="source"></MapperFilter></MapperMetaTag>
</metaInformation>
-->