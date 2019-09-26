START : 2018-07-16 13:35:39

                UPDATE TM_HO_CAPEX 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = 'D00057',
                    RK_ID = '51',
                    CAPEX_DESCRIPTION = 'Pembelian Vidcons',
                    CORE_CODE = 'SITE',
                    COMP_CODE = '11',
                    BA_CODE = '1121',
                    COA_CODE = '6201011102',
                    CAPEX_JAN = '0.00',
                    CAPEX_FEB = '120,000,000.00',
                    CAPEX_MAR = '0.00',
                    CAPEX_APR = '0.00',
                    CAPEX_MAY = '0.00',
                    CAPEX_JUN = '0.00',
                    CAPEX_JUL = '0.00',
                    CAPEX_AUG = '0.00',
                    CAPEX_SEP = '0.00',
                    CAPEX_OCT = '0.00',
                    CAPEX_NOV = '0.00',
                    CAPEX_DEC = '0.00',
                    CAPEX_TOTAL = '120,000,000.00'
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr9RAAaAACakOAAA';
            
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
                    'D00057',
                    '54',
                    'Keterangan Cloud',
                    'SITE',
                    '11',
                    '1121',
                    '6201011201',
                    '50.00',
                    '50.00',
                    '50.00',
                    '50.00',
                    '50.00',
                    '50.00',
                    '50.00',
                    '50.00',
                    '50.00',
                    '50.00',
                    '50.00',
                    '50.00',
                    '600.00',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            COMMIT;
END : 2018-07-16 13:35:40