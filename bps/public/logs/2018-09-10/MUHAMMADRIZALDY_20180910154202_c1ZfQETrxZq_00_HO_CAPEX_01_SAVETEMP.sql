START : 2018-09-10 15:42:02

                INSERT INTO TM_HO_CAPEX (
                    PERIOD_BUDGET,
                    CC_CODE,
                    RK_ID,
                    CAPEX_DESCRIPTION,
                    CORE_CODE,
                    COMP_CODE,
                    BA_CODE,
                    COA_CODE,
                    CAPEX_JAN,
                    CAPEX_FEB,
                    CAPEX_MAR,
                    CAPEX_APR,
                    CAPEX_MAY,
                    CAPEX_JUN,
                    CAPEX_JUL,
                    CAPEX_AUG,
                    CAPEX_SEP,
                    CAPEX_OCT,
                    CAPEX_NOV,
                    CAPEX_DEC,
                    CAPEX_TOTAL,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    '067',
                    '245',
                    'Pembelian Laptop',
                    'SITE',
                    '41',
                    '4122',
                    '1207010601',
                    '0',
                    '0',
                    '0',
                    '15000000',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '15000000',
                    'MUHAMMAD.RIZALDY',
                    SYSDATE
                );
            COMMIT;
END : 2018-09-10 15:42:03
