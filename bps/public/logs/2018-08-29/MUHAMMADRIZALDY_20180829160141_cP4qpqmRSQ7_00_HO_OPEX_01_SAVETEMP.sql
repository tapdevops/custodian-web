START : 2018-08-29 16:01:41

                UPDATE TM_HO_OPEX 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = '017',
                    RK_ID = '223',
                    OPEX_DESCRIPTION = 'Penginapan Manager Kebun',
                    CORE_CODE = 'HO',
                    COMP_CODE = '',
                    BA_CODE = '',
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
            
                INSERT INTO TM_HO_OPEX (
                    PERIOD_BUDGET,
                    CC_CODE,
                    RK_ID,
                    OPEX_DESCRIPTION,
                    CORE_CODE,
                    COMP_CODE,
                    BA_CODE,
                    COA_CODE,
                    OPEX_JAN,
                    OPEX_FEB,
                    OPEX_MAR,
                    OPEX_APR,
                    OPEX_MAY,
                    OPEX_JUN,
                    OPEX_JUL,
                    OPEX_AUG,
                    OPEX_SEP,
                    OPEX_OCT,
                    OPEX_NOV,
                    OPEX_DEC,
                    OPEX_TOTAL,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    '017',
                    '223',
                    'Snack Untuk Review',
                    'HO',
                    '11',
                    '1111',
                    '6201011502',
                    '187662',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '187662',
                    'MUHAMMAD.RIZALDY',
                    SYSDATE
                );
            COMMIT;
END : 2018-08-29 16:01:41
