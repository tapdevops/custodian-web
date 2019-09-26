START : 2018-07-18 17:27:55

                UPDATE TM_HO_CAPEX 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = 'D00057',
                    RK_ID = '48',
                    CAPEX_DESCRIPTION = 'Keterangan Cloud',
                    CORE_CODE = 'SITE',
                    COMP_CODE = '11',
                    BA_CODE = '1121',
                    COA_CODE = '6201011201',
                    CAPEX_JAN = '50.00',
                    CAPEX_FEB = '50.00',
                    CAPEX_MAR = '50.00',
                    CAPEX_APR = '50.00',
                    CAPEX_MAY = '50.00',
                    CAPEX_JUN = '50.00',
                    CAPEX_JUL = '50.00',
                    CAPEX_AUG = '50.00',
                    CAPEX_SEP = '50.00',
                    CAPEX_OCT = '50.00',
                    CAPEX_NOV = '50.00',
                    CAPEX_DEC = '50.00',
                    CAPEX_TOTAL = '600.00',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr9RAAaAACakPAAA';
            COMMIT;
END : 2018-07-18 17:27:55
