START : 2018-08-31 14:49:26

                INSERT INTO TR_HO_REPORT_VALID (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    GROUP_BUDGET,
                    OUTLOOK,
                    NEXT_BUDGET,
                    VAR_SELISIH,
                    VAR_PERSEN,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00025',
                    '009',
                    'OPEX',
                    '134651399',
                    '21634176',
                    '-113017223',
                    '-83.93',
                    'MUHAMMAD.RIZALDY',
                    SYSDATE
                );
            
                INSERT INTO TR_HO_REPORT_VALID (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    GROUP_BUDGET,
                    OUTLOOK,
                    NEXT_BUDGET,
                    VAR_SELISIH,
                    VAR_PERSEN,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00025',
                    '009',
                    'CAPEX',
                    '0',
                    '139584176',
                    '139584176',
                    '0',
                    'MUHAMMAD.RIZALDY',
                    SYSDATE
                );
            
                INSERT INTO TR_HO_REPORT_VALID (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    GROUP_BUDGET,
                    OUTLOOK,
                    NEXT_BUDGET,
                    VAR_SELISIH,
                    VAR_PERSEN,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00025',
                    '009',
                    'TOTAL',
                    '134651399',
                    '161218352',
                    '26566953',
                    '-83.93',
                    'MUHAMMAD.RIZALDY',
                    SYSDATE
                );
            COMMIT;
END : 2018-08-31 14:49:27
