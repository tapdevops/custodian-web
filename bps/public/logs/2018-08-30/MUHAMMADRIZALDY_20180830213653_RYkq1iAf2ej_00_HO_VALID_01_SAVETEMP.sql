START : 2018-08-30 21:36:53

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
                    'D00003',
                    '015',
                    'OPEX',
                    '200000000',
                    '0',
                    '-200000000',
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
                    'D00003',
                    '015',
                    'CAPEX',
                    '0',
                    '100000000',
                    '100000000',
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
                    'D00003',
                    '015',
                    'TOTAL',
                    '200000000',
                    '100000000',
                    '-100000000',
                    '0',
                    'MUHAMMAD.RIZALDY',
                    SYSDATE
                );
            COMMIT;
END : 2018-08-30 21:36:53
