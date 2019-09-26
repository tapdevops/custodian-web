START : 2018-09-11 16:30:38

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
                    'D00011',
                    '067',
                    'OPEX',
                    '2615187650880',
                    '16104506',
                    '-2615171546374',
                    '-99.99',
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
                    'D00011',
                    '067',
                    'CAPEX',
                    '0',
                    '49028612',
                    '49028612',
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
                    'D00011',
                    '067',
                    'TOTAL',
                    '2615187650880',
                    '65133118',
                    '-2615122517762',
                    '-99.99',
                    'MUHAMMAD.RIZALDY',
                    SYSDATE
                );
            COMMIT;
END : 2018-09-11 16:30:38
