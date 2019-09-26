START : 2018-07-19 09:03:56

                UPDATE TM_HO_OPEX 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = 'D00057',
                    RK_ID = '55',
                    OPEX_DESCRIPTION = 'Testing Keterangan Regular',
                    CORE_CODE = 'SITE',
                    COMP_CODE = '11',
                    BA_CODE = '1121',
                    COA_CODE = '6201011202',
                    OPEX_JAN = '200.00',
                    OPEX_FEB = '200.00',
                    OPEX_MAR = '200.00',
                    OPEX_APR = '200.00',
                    OPEX_MAY = '200.00',
                    OPEX_JUN = '200.00',
                    OPEX_JUL = '200.00',
                    OPEX_AUG = '200.00',
                    OPEX_SEP = '200.00',
                    OPEX_OCT = '200.00',
                    OPEX_NOV = '200.00',
                    OPEX_DEC = '500.00',
                    OPEX_TOTAL = '2,700.00',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAsBTAAaAACakfAAA';
            COMMIT;
END : 2018-07-19 09:03:56
