START : 2018-09-10 20:37:40

                UPDATE TM_HO_ACT_OUTLOOK 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = '015',
                    COA_CODE = '7101010501',
                    TRANSACTION_DESC = 'BI INTEREST PAYMENT PER 13 MAR-13 APR 2018 4',
                    CORE = 'HO',
                    COMP_CODE = '51',
                    ACT_JAN = '0',
                    ACT_FEB = '0',
                    ACT_MAR = '0',
                    ACT_APR = '-94826115',
                    ACT_MAY = '0',
                    ACT_JUN = '0',
                    ACT_JUL = '0',
                    ACT_AUG = '0',
                    OUTLOOK_SEP = '0',
                    OUTLOOK_OCT = '0',
                    OUTLOOK_NOV = '0',
                    OUTLOOK_DEC = '0',
                    YTD_ACTUAL = '-94826115',
                    ADJ = '94826115',
                    OUTLOOK = '0',
                    TOTAL_ACTUAL = '0',
                    UPDATE_USER = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr79AAeAAAx5XAAE';
            
                INSERT INTO TM_HO_ACT_OUTLOOK (
                    PERIOD_BUDGET,
                    CC_CODE,
                    COA_CODE,
                    TRANSACTION_DESC,
                    CORE,
                    COMP_CODE,
                    ACT_JAN,
                    ACT_FEB,
                    ACT_MAR,
                    ACT_APR,
                    ACT_MAY,
                    ACT_JUN,
                    ACT_JUL,
                    ACT_AUG,
                    OUTLOOK_SEP,
                    OUTLOOK_OCT,
                    OUTLOOK_NOV,
                    OUTLOOK_DEC,
                    YTD_ACTUAL,
                    ADJ,
                    OUTLOOK,
                    TOTAL_ACTUAL,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    '015',
                    '6201010401',
                    'Stock Opname',
                    'HO',
                    '61',
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
                    '6000000',
                    '0',
                    '0',
                    '0',
                    '6000000',
                    '6000000',
                    'MUHAMMAD.RIZALDY',
                    SYSDATE
                );
            COMMIT;
END : 2018-09-10 20:37:40