START : 2018-08-20 15:52:10

                UPDATE TM_HO_CAPEX 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = 'D00057',
                    RK_ID = '3',
                    CAPEX_DESCRIPTION = 'testing',
                    CORE_CODE = 'SITE',
                    COMP_CODE = '11',
                    BA_CODE = '1121',
                    COA_CODE = '5403010105',
                    CAPEX_JAN = '0',
                    CAPEX_FEB = '3000000',
                    CAPEX_MAR = '0',
                    CAPEX_APR = '0',
                    CAPEX_MAY = '0',
                    CAPEX_JUN = '0',
                    CAPEX_JUL = '0',
                    CAPEX_AUG = '0',
                    CAPEX_SEP = '0',
                    CAPEX_OCT = '0',
                    CAPEX_NOV = '0',
                    CAPEX_DEC = '0',
                    CAPEX_TOTAL = '3000000',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr9RAAaAACakLAAA';
            COMMIT;
END : 2018-08-20 15:52:13
