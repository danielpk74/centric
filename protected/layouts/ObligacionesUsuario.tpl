<com:TActivePanel ID="APnlTareasAsignadas" Style="display:none; position:relative">
                            
            <h1>Resumen de tareas por usuario </h1>
            <hr/>
            <br/>
            
            <com:TRepeater ID="DRTareasUsuarios" OnItemCreated="OnItemCreated">
                <prop:HeaderTemplate>        
                </prop:HeaderTemplate>

                <prop:ItemTemplate>
                    
                    <h1>Gestor <%#$this->DataItem->Usuario%> - <%#$this->DataItem->Nombres %> <%#$this->DataItem->Apellidos %></h1>

                    <com:TActiveDataGrid 
                        ID="ADGObligaciones"
                        AutoGenerateColumns="false"
                        CssClass="datagrid-datos"
                        HeaderStyle.CssClass="header-fondo"
                        ItemStyle.CssClass="item-style"
                        AlternatingItemStyle.CssClass="alternating-style"
                        AllowSorting="true"
                        AllowPaging="true"
                        PageSize="10"
                        PagerStyle.CssClass="grid-pager"
                        PagerStyle.Mode="Numeric"
                        OnSortCommand="sortData">

                        <com:TBoundColumn Id="ClmCodObligacion" DataField="CodObligacion" HeaderText="Cod" HeaderStyle.Width="20px" Visible="true"/>
                        <com:TBoundColumn Id="ClmFhObligacion" DataField="Obligacion.FhObligacion" HeaderText="F. Obligacion" HeaderStyle.Width="20px" />
                        <com:TBoundColumn Id="ClmFhReporte" DataField="Obligacion.FhReporte"  HeaderText="Reportado" HeaderStyle.Width="20px" />
                        <com:TBoundColumn Id="ClmFhUltimoPago" DataField="Obligacion.FhUltimoPago" HeaderText="F. Ult Pago" HeaderStyle.Width="20px" />
                        <com:TBoundColumn Id="ClmIdTercero" DataField="Obligacion.IdTercero" HeaderText="Identificacion" HeaderStyle.Width="20px" />
                        <com:TBoundColumn Id="ClmNmTercero" DataField="NombreCorto" HeaderText="Moroso" HeaderStyle.Width="20px" />
                        <com:TBoundColumn Id="ClmNrObligacion" DataField="Obligacion.NrObligacion" HeaderText="Nro Oblig" HeaderStyle.Width="20px" />
                        <com:TBoundColumn Id="ClmValorCuota" DataField="Obligacion.ValorCuota" HeaderText="Vr. Cuota" HeaderStyle.Width="20px" />
                        <com:TBoundColumn Id="ClmEstado" DataField="Obligacion.Estado" HeaderText="Estado" HeaderStyle.Width="20px" />
                        <com:TBoundColumn Id="ClmSaldo" DataField="Obligacion.Saldo" HeaderText="Saldo" HeaderStyle.Width="20px" />
                        <com:TBoundColumn Id="ClmIntereses" DataField="Obligacion.Intereses" HeaderText="Intereses" HeaderStyle.Width="20px" />

                    </com:TActiveDataGrid>
            
            </prop:ItemTemplate>
          </com:TRepeater>
         </com:TActivePanel>