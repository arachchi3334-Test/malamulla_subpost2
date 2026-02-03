<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0">
	<xsl:output indent="no" method="xml" encoding="utf-8"/>
	<xsl:variable name="TDcounter" select="0"/>
	<!--<xsl:template match="*">
		<xsl:element name="{name()}">
			<xsl:copy-of select="@*"/>
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>-->

	<xsl:template match="table">
		<Table>
			<xsl:if test="@id">
				<xsl:attribute name="id">
					<xsl:value-of select="@id"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@frame">
				<xsl:attribute name="frame">
					<xsl:choose>
						<xsl:when test="@frame='vsides'">sides</xsl:when>
						<xsl:when test="@frame='above'">top</xsl:when>
						<xsl:when test="@frame='below'">bottom</xsl:when>
						<xsl:when test="@frame='hsides'">topbot</xsl:when>
						<xsl:when test="@frame='box'">all</xsl:when>
						<xsl:when test="@frame=''">none</xsl:when>
						<xsl:otherwise>all</xsl:otherwise>	<!-- default -->
					</xsl:choose>				
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-bgshade">
				<xsl:attribute name="bgshade">
					<xsl:value-of select="@data-bgshade"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-orient">
				<xsl:attribute name="orient">
					<xsl:value-of select="@data-orient"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-style">
				<xsl:attribute name="style">
					<xsl:value-of select="@data-style"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-pagewide">
				<xsl:attribute name="pagewide">
					<xsl:value-of select="@data-pagewide"/>
				</xsl:attribute>
			</xsl:if>
			<tgroup>
				<!--<xsl:copy-of select="child::colgroup/@*"/>-->
				<xsl:if test="child::colgroup/@data-cols">
					<xsl:attribute name="cols">
						<xsl:value-of select="child::colgroup/@data-cols"/>
					</xsl:attribute>
				</xsl:if>
				<xsl:if test="child::colgroup/@data-valign">
					<xsl:attribute name="valign">
						<xsl:value-of select="child::colgroup/@data-valign"/>
					</xsl:attribute>
				</xsl:if>
				<xsl:if test="child::colgroup/@data-colsep">
					<xsl:attribute name="colsep">
						<xsl:value-of select="child::colgroup/@data-colsep"/>
					</xsl:attribute>
				</xsl:if>
				<xsl:if test="child::colgroup/@data-rowsep">
					<xsl:attribute name="rowsep">
						<xsl:value-of select="child::colgroup/@data-rowsep"/>
					</xsl:attribute>
				</xsl:if>
				
				<xsl:choose>
					<xsl:when test="child::colgroup">
						<xsl:for-each select="child::colgroup/col">
							<xsl:call-template name="colConversion">
								<xsl:with-param name="colWidth">
									<xsl:value-of select="@width"/>
								</xsl:with-param>
							</xsl:call-template>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<xsl:variable name="firstTDVal" select=".//tbody/tr[1]/td[1]/@style"/>
						<xsl:choose>
							<xsl:when test="starts-with($firstTDVal, 'width:')">
								<xsl:for-each select=".//tbody/tr[1]/td">
									<xsl:variable name="WidthVal" select="./@style"/>
									<xsl:call-template name="colConversion">
										<xsl:with-param name="colWidth">
											<xsl:value-of select="substring-before(substring-after($WidthVal, 'width: '), 'px')"/><xsl:text>%</xsl:text>
										</xsl:with-param>
									</xsl:call-template>
								</xsl:for-each>
							</xsl:when>
							<xsl:otherwise>
								<xsl:variable name="countFirstROWTD">
									<xsl:value-of select="count(.//tbody/tr[1]/td[not(@colspan)])"/>
								</xsl:variable>
								<xsl:for-each select=".//tbody/tr[1]/td">
									<xsl:call-template name="colConversion">
										<xsl:with-param name="colWidth">
											<xsl:value-of select="100 div $countFirstROWTD"/><xsl:text>*</xsl:text>
										</xsl:with-param>
									</xsl:call-template>
								</xsl:for-each>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:apply-templates select="thead"/>
				<xsl:apply-templates select="tbody"/>
			</tgroup>
		</Table>
		<xsl:if test="child::tfoot">
			<TableFootNotes>
				<xsl:for-each select="child::tfoot/descendant::tr">
					<table-foot-note>
							<xsl:attribute name="callout">
								<xsl:value-of select="child::td/@data-callout"/>
							</xsl:attribute>
							<xsl:attribute name="id">
								<xsl:value-of select="child::td/@data-id"/>
							</xsl:attribute>
							<xsl:value-of select="current()"/>
					</table-foot-note>
				</xsl:for-each>
			</TableFootNotes>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="tgroup">
		<!--<xsl:variable name="total-percents-colwidth"><xsl:call-template name="total-width"/></xsl:variable>-->
		<colgroup>
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
	
	<xsl:template match="tfoot"/>
	
	<xsl:template name="colConversion">
		<!--<xsl:param name="total-percents-colwidth" select="'1'"/>-->
		<xsl:param name="colWidth"/>
		<colspec>
			<xsl:if test="$colWidth!=''">
				<xsl:attribute name="colwidth">
				<xsl:choose>
					<xsl:when test="contains($colWidth,'%')">
						<xsl:value-of select="substring-before($colWidth,'%')"/><xsl:text>*</xsl:text>
					</xsl:when>
					<!--<xsl:when test="contains(@colwidth,'*')">
						<!-\-<xsl:value-of select="{100 * number(substring-before(@colwidth,'*')) divnumber($total-percents-colwidth)}%"/>-\->
						<xsl:value-of select="substring-before(@colwidth,'*')"/>
					</xsl:when>-->
					<xsl:otherwise>
						<xsl:value-of select="$colWidth"/><xsl:text>pt</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-colnum">
				<xsl:attribute name="colnum">
					<xsl:value-of select="@data-colnum"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-colname">
				<xsl:attribute name="colname">
					<xsl:value-of select="@data-colname"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-align">
				<xsl:attribute name="align">
					<xsl:value-of select="@data-align"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-valign">
				<xsl:attribute name="valign">
					<xsl:value-of select="@data-valign"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-colsep">
				<xsl:attribute name="colsep">
					<xsl:value-of select="@data-colsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-rowsep">
				<xsl:attribute name="rowsep">
					<xsl:value-of select="@data-rowsep"/>
				</xsl:attribute>
			</xsl:if>
		</colspec>
	</xsl:template>
	
	<xsl:template match="thead">
		<thead>
			<xsl:apply-templates/>
		</thead>
	</xsl:template>
	
	<xsl:template match="tbody">
		<tbody>
			<xsl:apply-templates />
		</tbody>
	</xsl:template>
	
	<xsl:template match="tr">
		<row>
			<xsl:if test="@data-rowsep">
				<xsl:attribute name="rowsep">
					<xsl:value-of select="@data-rowsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-valign">
				<xsl:attribute name="valign">
					<xsl:value-of select="@data-valign"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-bgshade">
				<xsl:attribute name="bgshade">
					<xsl:value-of select="@data-bgshade"/>
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
		</row>
	</xsl:template>
	
	<xsl:template match="td">
		<entry>
			<xsl:if test="@align">
				<xsl:attribute name="align">
					<xsl:value-of select="@align"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-rowsep">
				<xsl:attribute name="rowsep">
					<xsl:value-of select="@data-rowsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-colsep">
				<xsl:attribute name="colsep">
					<xsl:value-of select="@data-colsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-colname">
				<xsl:choose>
					<xsl:when test="@colspan"></xsl:when>
					<xsl:otherwise>
						<xsl:attribute name="colname">
							<xsl:value-of select="@data-colname"/>
						</xsl:attribute>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
			<xsl:if test="@colspan!=''">
				<xsl:variable name="nameendVal1" select="substring-after(@data-colname, 'col')"/>
				<xsl:variable name="nameendVal2" select="number(@colspan)-1"/>
				<xsl:attribute name="namest"><xsl:value-of select="@data-colname"/></xsl:attribute>
				<xsl:attribute name="nameend"><xsl:text>col</xsl:text><xsl:value-of select="number($nameendVal1) + number($nameendVal2)"/></xsl:attribute>
			</xsl:if>
			<!--<xsl:if test="@namest!='' and @nameend!=''">
				<xsl:variable name="namestVal" select="substring-after(@namest, 'col')"/>
				<xsl:variable name="nameendVal" select="substring-after(@nameend, 'col')"/>
				<xsl:attribute name="colspan"><xsl:value-of select="number($nameendVal) - number($namestVal)+1"/></xsl:attribute>
			</xsl:if>-->
			<xsl:if test="@rowspan">
				<xsl:attribute name="morerows"><xsl:value-of select="number(@rowspan)-1"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-valign">
				<xsl:attribute name="valign">
					<xsl:value-of select="@data-valign"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:apply-templates/>
		</entry>
	</xsl:template>
	
	<xsl:template match="th">
		<entry>
			<xsl:if test="@align">
				<xsl:attribute name="align">
					<xsl:value-of select="@align"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-rowsep">
				<xsl:attribute name="rowsep">
					<xsl:value-of select="@data-rowsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-colsep">
				<xsl:attribute name="colsep">
					<xsl:value-of select="@data-colsep"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-colname">
				<xsl:choose>
					<xsl:when test="@colspan"></xsl:when>
					<xsl:otherwise>
						<xsl:attribute name="colname">
						<xsl:value-of select="@data-colname"/>
					</xsl:attribute>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
			<xsl:if test="@colspan!=''">
				<xsl:variable name="nameendVal1" select="substring-after(@data-colname, 'col')"/>
				<xsl:variable name="nameendVal2" select="number(@colspan)-1"/>
				<xsl:attribute name="namest"><xsl:value-of select="@data-colname"/></xsl:attribute>
				<xsl:attribute name="nameend"><xsl:text>col</xsl:text><xsl:value-of select="number($nameendVal1) + number($nameendVal2)"/></xsl:attribute>
			</xsl:if>
			<!--<xsl:if test="@namest!='' and @nameend!=''">
				<xsl:variable name="namestVal" select="substring-after(@namest, 'col')"/>
				<xsl:variable name="nameendVal" select="substring-after(@nameend, 'col')"/>
				<xsl:attribute name="colspan"><xsl:value-of select="number($nameendVal) - number($namestVal)+1"/></xsl:attribute>
			</xsl:if>-->
			<xsl:if test="@rowspan">
				<xsl:attribute name="morerows"><xsl:value-of select="number(@rowspan)-1"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="@data-valign">
				<xsl:attribute name="valign">
					<xsl:value-of select="@data-valign"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:apply-templates/>
		</entry>
	</xsl:template>
	
	<xsl:template match="caption">
		<table-heading>
			<xsl:apply-templates/>
		</table-heading>
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
	
	<!--<xsl:template match="colgroup"><xsl:apply-templates /></xsl:template>-->
	
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
</xsl:stylesheet>