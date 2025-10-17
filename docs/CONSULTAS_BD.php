<?php
"CREATE TRIGGER crear_admision       
 ON citas      
 AFTER UPDATE   AS   BEGIN   if update(asistio)   BEGIN    
 
 /********* encabezado pagosr ********/     
 declare @id as numeric(18,0);     
 declare @consecu as varchar(10);     
 declare @cod_cli as varchar(10);    
 declare @fechact as smalldatetime;     
 declare @tipopagoa as varchar(12);    
 declare @prefijoa as varchar(2);     
 declare @detallea as varchar(10);     
 declare @ced_profa as varchar(20);     
 declare @finalidad as varchar(2);     
 declare @personal as varchar(2);    
 declare @concepto as varchar(2);     
 declare @realizacion as varchar(2);    
 declare @ambito as varchar(1);    
 declare @entsuba as varchar(10);    
 declare @fecha_fa as smalldatetime;    
 --RQ48915 Gilberto - incluyo contrato     
 declare @contrato as varchar(30);  
 
 /*******falta el centro de costos******/    
 declare @sedea as varchar(3);     
 declare @tarifaa as varchar(10);     
 declare @elaboraa as varchar(15);     
 declare @usuarioa as varchar(30);       
 declare @parameautoriza as bit;    
 declare @paramemultarifas as bit;       
 declare @cod_prod as varchar(10);     
 declare @autorizacion as varchar(50);       
 declare @nombrepaciente as varchar (150);     
 declare @cedpaciente as varchar (50);       
 set @id = (select id from inserted);      
 /*se unifica la asignación de variables de citas en una sola consulta 07-09-2022 */    
 select @cod_cli = nro_hist, 
		@fechact = fecha,
		@detallea = codent, 
		@ced_profa = cedprof, 
		@entsuba = codent2, 
		@sedea = sede,
		@usuarioa = registro, 
		@cod_prod = tiempo, 
		@autorizacion = autoriz,
		@elaboraa = ced_usu     
		from citas where id = @id     
/*se unifica la asignación de variables de cliente en una sola consulta 07-09-2022 */     
 select @contrato = contrato, 
		@nombrepaciente = nombre,
		@cedpaciente = nit_cli 
		from cliente where codigo = @cod_cli            
		--- set @fechact = getdate() req 38610.00  se comentarea   para que tome la fecha de la cita y no la fecha actual       
 set @tipopagoa = 'AUTORIZACIÓN'     
 set @prefijoa = 'OR'         
 set @finalidad = (select texto_control from valores_controles where ruta_control='txtfinal')     
 set @personal = (select texto_control from valores_controles where ruta_control='txtpersonal')     
 set @concepto = (select texto_control from valores_controles where ruta_control='txtconcepto')    
 set @realizacion = (select texto_control from valores_controles where ruta_control='txtformar')     
 set @ambito = '1'       
 set @fecha_fa = getdate()       
 set @tarifaa = (select tarifa from entidades where codigo = @entsuba and admini = @detallea)       
 --set @elaboraa = (select cedula from usuarios where ltrim(rtrim(usuario)) = @usuarioa)     
 --RQ48915 Gilberto - establezco valor para la variable @contrato         
 set @paramemultarifas = (select multiplestarifas from mod_administra)     
 set @parameautoriza = (select rautoriza from Parame)     
 
 /********* fin encabezado *********/ 
 

 /******* Recalcular consecutivo *******/     
 declare @consecu_nuevo as varchar(8);        
 /******* fin recalcular *******/     
 
 /******* detalle pagodet *******/       
 declare @valor as numeric(10);     
 declare @valor_tmp as numeric(10);     
 declare @codcedprof as varchar(10);     
 declare @nomproc as varchar (250);     
 declare @codconta as varchar(20);           
 set @codcedprof = (select codigo from emplea where ecc = @ced_profa)     
 
 /*se unifica la asignación de variables de procdent en una sola consulta 07-09-2022 */     
 select @nomproc = descrip, 
		@codconta = codesp 
		from procdent where cod_enti = @tarifaa AND LTRIM(codigo) = @cod_prod       
		
/********* detalle ***********/    

/**Se añadio la verificacion si la asistencia es por el sistema nuevo o antiguo con la columna clinico_nuevo */
IF EXISTS (SELECT nro_hist FROM citas WHERE id = @id AND asistio = 1 AND (clinico_nuevo IS NULL OR clinico_nuevo = 0))
BEGIN
    IF EXISTS (SELECT adm_automat FROM MOD_factura WHERE adm_automat = 1)
    BEGIN
        IF EXISTS (
            SELECT tarifa
            FROM entidades
            WHERE codigo = @entsuba
              AND admini = @detallea
              AND adm_automatcit = 1
        )
        BEGIN
            IF EXISTS (SELECT id_pagosr FROM citas WHERE id = @id AND id_pagosr = 0)
            BEGIN
                IF NOT EXISTS (SELECT idcita FROM pagosr WHERE idcita = @id)
                BEGIN
                    /******* busca en la tabla excepcion_tar ******/
                    IF EXISTS (
                        SELECT precio
                        FROM excepcion_tar
                        WHERE LTRIM(entidad) = @detallea
                          AND LTRIM(subentidad) = @entsuba
                          AND LTRIM(tarifa) = @tarifaa
                          AND LTRIM(codigo) = @cod_prod
                    )
                    BEGIN
                        IF NOT EXISTS (
                            SELECT s1.valor
                            FROM Subprocedimientos s1
                            LEFT JOIN SubprocedimientosMant s2
                                   ON s2.codSubproc = s1.codSubProc
                            WHERE codEntidad = @detallea
                              AND codsubentidad = @entsuba
                              AND codtarifa = @tarifaa
                              AND codProc = @cod_prod
                        )
                        BEGIN
                            SET @valor = (
                                SELECT precio
                                FROM excepcion_tar
                                WHERE LTRIM(entidad) = @detallea
                                  AND LTRIM(subentidad) = @entsuba
                                  AND LTRIM(tarifa) = @tarifaa
                                  AND LTRIM(codigo) = @cod_prod
                            )
                        END
                        ELSE
                        BEGIN
                            SET @valor = (
                                SELECT CASE
                                           WHEN s1.porcentaje > 0
                                               THEN ROUND(ISNULL(s2.valor, 0) * (1 + (s1.porcentaje / 100)), 0)
                                           ELSE s1.valor
                                       END AS valororiginal
                                FROM Subprocedimientos s1
                                LEFT JOIN SubprocedimientosMant s2
                                       ON s2.codSubproc = s1.codSubProc
                                WHERE codEntidad = @detallea
                                  AND codsubentidad = @entsuba
                                  AND codtarifa = @tarifaa
                                  AND codProc = @cod_prod
                            )
                        END
                    END
                    ELSE
                    BEGIN
                        SET @valor_tmp = (
                            SELECT precio
                            FROM procdent
                            WHERE cod_enti = @tarifaa
                              AND LTRIM(codigo) = @cod_prod
                              AND inactivo = 0
                        )

                        SET @valor = (
                            SELECT CASE
                                       WHEN e.porcma > 0 AND e.porcme > 0 AND e.coopago = 0
                                           THEN CASE
                                                    WHEN SUBSTRING(t.nombre, 1, 4) = 'SOAT'
                                                        THEN ROUND(
                                                            ((@valor_tmp * e.porcma) / 100 + @valor_tmp)
                                                            - (((@valor_tmp * e.porcma) / 100 + @valor_tmp) * e.porcme / 100),
                                                            -2
                                                        )
                                                    ELSE ((@valor_tmp * e.porcma) / 100 + @valor_tmp)
                                                         - (((@valor_tmp * e.porcma) / 100 + @valor_tmp) * e.porcme / 100)
                                                END
                                       WHEN e.porcma = 0 AND e.porcme > 0 AND e.coopago = 0
                                           THEN CASE
                                                    WHEN SUBSTRING(t.nombre, 1, 4) = 'SOAT'
                                                        THEN ROUND(@valor_tmp - ((@valor_tmp * e.porcme) / 100), -2)
                                                    ELSE @valor_tmp - ((@valor_tmp * e.porcme) / 100)
                                                END
                                       WHEN e.porcma > 0 AND e.porcme = 0 AND e.coopago = 0
                                           THEN CASE
                                                    WHEN SUBSTRING(t.nombre, 1, 4) = 'SOAT'
                                                        THEN ROUND(@valor_tmp + ((@valor_tmp * e.porcma) / 100), -2)
                                                    ELSE @valor_tmp + ((@valor_tmp * e.porcma) / 100)
                                                END
                                       WHEN e.porcma > 0 AND e.porcme > 0 AND e.coopago > 0
                                           THEN CASE
                                                    WHEN SUBSTRING(t.nombre, 1, 4) = 'SOAT'
                                                        THEN ROUND(
                                                            ((@valor_tmp * e.porcma) / 100 + @valor_tmp)
                                                            - (((@valor_tmp * e.porcma) / 100 + @valor_tmp) * e.porcme / 100),
                                                            -2
                                                        )
                                                    ELSE ((@valor_tmp * e.porcma) / 100 + @valor_tmp)
                                                         - (((@valor_tmp * e.porcma) / 100 + @valor_tmp) * e.porcme / 100)
                                                END
                                       WHEN e.porcma = 0 AND e.porcme > 0 AND e.coopago > 0
                                           THEN CASE
                                                    WHEN SUBSTRING(t.nombre, 1, 4) = 'SOAT'
                                                        THEN ROUND(@valor_tmp - ((@valor_tmp * e.porcme) / 100), -2)
                                                    ELSE @valor_tmp - ((@valor_tmp * e.porcme) / 100)
                                                END
                                       WHEN e.porcma > 0 AND e.porcme = 0 AND e.coopago > 0
                                           THEN CASE
                                                    WHEN SUBSTRING(t.nombre, 1, 4) = 'SOAT'
                                                        THEN ROUND(@valor_tmp + ((@valor_tmp * e.porcma) / 100), -2)
                                                    ELSE @valor_tmp + ((@valor_tmp * e.porcma) / 100)
                                                END
                                       WHEN e.porcma = 0 AND e.porcme = 0
                                           THEN CASE
                                                    WHEN SUBSTRING(t.nombre, 1, 4) = 'SOAT'
                                                        THEN ROUND(@valor_tmp, -2)
                                                    ELSE @valor_tmp
                                                END
                                       ELSE CASE
                                                WHEN SUBSTRING(t.nombre, 1, 4) = 'SOAT'
                                                    THEN ROUND(@valor_tmp, -2)
                                                ELSE @valor_tmp
                                            END
                                   END AS precio
                            FROM entidades e
                            INNER JOIN tarifas t ON e.tarifa = t.codigo
                            WHERE e.codigo = @entsuba
                              AND e.admini = @detallea
                              AND e.tarifa = @tarifaa
                        )
                    END

                    -- RQ48855 Gilberto - si el valor está en cero o es NULL no debe continuar
                    IF @valor IS NULL OR @valor = 0
                    BEGIN
                        RETURN
                    END

                    SET @consecu = (
                        SELECT LTRIM(RTRIM(prefijo)) + '' + LTRIM(RTRIM(consecu)) AS consecu
                        FROM con_inv
                        WHERE sigla = 'OR'
                    )

                    SET @consecu_nuevo = (
                        SELECT RIGHT('000000000000000' + CAST(MAX(CAST(consecu AS INT)) + 1 AS VARCHAR(8)), MAX(LEN(consecu))) AS cod
                        FROM con_inv
                        WHERE sigla = 'OR'
                    )

                    DECLARE @idadmin NUMERIC(10);

                    SET @idadmin = (
                        SELECT id
                        FROM pagosr
                        WHERE recibo = @consecu
                    )

                    IF NOT EXISTS (SELECT recibo FROM pagosr WHERE recibo = @consecu)
                    BEGIN
                        IF @parameautoriza = 1
                        BEGIN
                            IF EXISTS (
                                SELECT procedi
                                FROM autorizad
                                WHERE LTRIM(historia) = @cod_cli
                                  AND LTRIM(n_autoriza) = @autorizacion
                                  AND LTRIM(procedi) = @cod_prod
                            )
                            BEGIN
                                IF EXISTS (
                                    SELECT procedi
                                    FROM autorizad
                                    WHERE LTRIM(historia) = @cod_cli
                                      AND LTRIM(n_autoriza) = @autorizacion
                                      AND LTRIM(procedi) = @cod_prod
                                      AND cantidad*2 >= cdispo
                                )
                                BEGIN 
                                    -- RQ48915 Gilberto - incluyo contrato en pagosr
                                    INSERT INTO pagosr (
                                        codigo, recibo, fecha, valor, tipopago, elabora, prefijo, detalle, ced_prof,
                                        ambito, finalidad, concepto, freal, personal, entsub, fecha_f, sede, tarifa,
                                        idcita, usuario, contrato
                                    )
                                    VALUES (
                                        @cod_cli, @consecu, @fechact, @valor, @tipopagoa, @elaboraa, @prefijoa,
                                        @detallea, @ced_profa, @ambito, @finalidad, @concepto, @realizacion,
                                        @personal, @entsuba, @fecha_fa, @sedea, @tarifaa, @id, @usuarioa, @contrato
                                    );

                                    INSERT INTO pagodet (
                                        nro, fecha, codigo, cedprof, nombre, cedula, cod_ing, descrip, vlr_ing,
                                        prefijo, cod_espec, cod_pcto, cant, nivel, sede, valor_siniva, usuario
                                    )
                                    VALUES (
                                        @consecu, @fechact, @cod_cli, @detallea, @nombrepaciente, @cedpaciente,
                                        @cod_prod, @nomproc, @valor, @prefijoa, @codconta, @ced_profa, 1,
                                        @autorizacion, @sedea, @valor, @usuarioa
                                    );

                                    INSERT INTO audi_sis (fecha, usuario, opcion, nuevo, tipo_doc, nro, informacion)
                                    VALUES (
                                        GETDATE(), @elaboraa, 'AGENDA', 1, 'ADM AUTO', @consecu,
                                        'SE CREÓ LA ADMISIÓN AUTOMÁTICAMENTE DESDE LA AGENDA CUANDO SE DIO ASISTIDO A LA CITA'
                                    );

                                    SET @idadmin = (SELECT id FROM pagosr WHERE recibo = @consecu);

                                    UPDATE citas
                                    SET id_pagosr = @idadmin
                                    WHERE id = @id;

                                    UPDATE con_inv
                                    SET consecu = @consecu_nuevo
                                    WHERE sigla = 'OR';

                                    UPDATE autorizad
                                    SET cdispo = cdispo + 1
                                    WHERE LTRIM(historia) = @cod_cli
                                      AND LTRIM(n_autoriza) = @autorizacion
                                      AND LTRIM(procedi) = @cod_prod;
                                END
                            END
                            ELSE
                            BEGIN
                                IF EXISTS (
                                    SELECT procedi
                                    FROM autoriza
                                    WHERE LTRIM(historia) = @cod_cli
                                      AND LTRIM(n_autoriza) = @autorizacion
                                      AND LTRIM(procedi) = @cod_prod
                                      AND cantidad <> cdispo
                                )
                                BEGIN
                                    -- RQ48915 Gilberto - incluyo contrato en pagosr
                                    INSERT INTO pagosr (
                                        codigo, recibo, fecha, valor, tipopago, elabora, prefijo, detalle, ced_prof,
                                        ambito, finalidad, concepto, freal, personal, entsub, fecha_f, sede, tarifa,
                                        idcita, usuario, contrato
                                    )
                                    VALUES (
                                        @cod_cli, @consecu, @fechact, @valor, @tipopagoa, @elaboraa, @prefijoa,
                                        @detallea, @ced_profa, @ambito, @finalidad, @concepto, @realizacion,
                                        @personal, @entsuba, @fecha_fa, @sedea, @tarifaa, @id, @usuarioa, @contrato
                                    );

                                    INSERT INTO pagodet (
                                        nro, fecha, codigo, cedprof, nombre, cedula, cod_ing, descrip, vlr_ing,
                                        prefijo, cod_espec, cod_pcto, cant, nivel, sede, valor_siniva, usuario
                                    )
                                    VALUES (
                                        @consecu, @fechact, @cod_cli, @detallea, @nombrepaciente, @cedpaciente,
                                        @cod_prod, @nomproc, @valor, @prefijoa, @codconta, @ced_profa, 1,
                                        @autorizacion, @sedea, @valor, @usuarioa
                                    );

                                    INSERT INTO audi_sis (fecha, usuario, opcion, nuevo, tipo_doc, nro, informacion)
                                    VALUES (
                                        GETDATE(), @elaboraa, 'AGENDA', 1, 'ADM AUTO', @consecu,
                                        'SE CREÓ LA ADMISIÓN AUTOMÁTICAMENTE DESDE LA AGENDA CUANDO SE DIO ASISTIDO A LA CITA'
                                    );

                                    SET @idadmin = (SELECT id FROM pagosr WHERE recibo = @consecu);

                                    UPDATE citas
                                    SET id_pagosr = @idadmin
                                    WHERE id = @id;

                                    UPDATE con_inv
                                    SET consecu = @consecu_nuevo
                                    WHERE sigla = 'OR';

                                    UPDATE autoriza
                                    SET cdispo = cdispo + 1
                                    WHERE LTRIM(historia) = @cod_cli
                                      AND LTRIM(n_autoriza) = @autorizacion
                                      AND LTRIM(procedi) = @cod_prod;
                                END
                            END
                        END
                        ELSE
                        BEGIN
                            IF @paramemultarifas = 1
                            BEGIN
                                -- RQ48915 Gilberto - incluyo contrato en pagosr
                                INSERT INTO pagosr (
                                    codigo, recibo, fecha, valor, tipopago, elabora, prefijo, detalle, ced_prof,
                                    ambito, finalidad, concepto, freal, personal, entsub, fecha_f, sede, tarifa,
                                    idcita, usuario, contrato
                                )
                                VALUES (
                                    @cod_cli, @consecu, @fechact, @valor, @tipopagoa, @elaboraa, @prefijoa,
                                    @detallea, @ced_profa, @ambito, @finalidad, @concepto, @realizacion,
                                    @personal, @entsuba, @fecha_fa, @sedea, @tarifaa, @id, @usuarioa, @contrato
                                );

                                INSERT INTO pagodet (
                                    nro, fecha, codigo, cedprof, nombre, cedula, cod_ing, descrip, vlr_ing,
                                    prefijo, cod_espec, cod_pcto, cant, sede, valor_siniva, usuario
                                )
                                VALUES (
                                    @consecu, @fechact, @cod_cli, @detallea, @nombrepaciente, @cedpaciente,
                                    @cod_prod, @nomproc, @valor, @prefijoa, @codconta, @ced_profa, 1,
                                    @sedea, @valor, @usuarioa
                                );

                                INSERT INTO audi_sis (fecha, usuario, opcion, nuevo, tipo_doc, nro, informacion)
                                VALUES (
                                    GETDATE(), @elaboraa, 'AGENDA', 1, 'ADM AUTO', @consecu,
                                    'SE CREÓ LA ADMISIÓN AUTOMÁTICAMENTE DESDE LA AGENDA CUANDO SE DIO ASISTIDO A LA CITA'
                                );

                                SET @idadmin = (SELECT id FROM pagosr WHERE recibo = @consecu);

                                UPDATE citas
                                SET id_pagosr = @idadmin
                                WHERE id = @id;

                                UPDATE con_inv
                                SET consecu = @consecu_nuevo
                                WHERE sigla = 'OR';
                            END
                        END
                    END
                END
            END
        END
    END
END
";