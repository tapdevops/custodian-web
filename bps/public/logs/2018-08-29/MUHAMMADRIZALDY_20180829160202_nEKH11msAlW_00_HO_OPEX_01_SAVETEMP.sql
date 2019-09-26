START : 2018-08-29 16:02:02

                UPDATE TM_HO_OPEX 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = '017',
                    RK_ID = '223',
                    OPEX_DESCRIPTION = 'Penginapan Manager Kebun',
                    CORE_CODE = 'HO',
                    COMP_CODE = '12',
                    BA_CODE = '1211',
                    COA_CODE = '6201010401',
                    OPEX_JAN = '0',
                    OPEX_FEB = '0',
                    OPEX_MAR = '0',
                    OPEX_APR = '5000000',
                    OPEX_MAY = '0',
                    OPEX_JUN = '0',
                    OPEX_JUL = '0',
                    OPEX_AUG = '0',
                    OPEX_SEP = '0',
                    OPEX_OCT = '0',
                    OPEX_NOV = '0',
                    OPEX_DEC = '0',
                    OPEX_TOTAL = '5000000',
                    UPDATE_USER = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAsBTAAaAACakbAAB';
            COMMIT;
END : 2018-08-29 16:02:02
