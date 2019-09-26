START : 2018-09-10 21:01:50

                INSERT INTO TR_HO_SUMMARY_OUTLOOK (
                    PERIOD_BUDGET,
                    CC_CODE,
                    COA_CODE,
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
                    ADJ,
                    YTD_ACTUAL_ADJ,
                    OUTLOOK,
                    LATEST_PERIOD,
                    ANNUALIZED_YTD,
                    VARIANCE_RP,
                    VARIANCE_PERSEN,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    '015',
                    '7101010501',
                    '2955411034',
                    '0',
                    '0',
                    '-94826115',
                    '0',
                    '0',
                    '4552945',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '94826115',
                    '2959963979',
                    '0',
                    '2959963979',
                    '4439945968.5',
                    '-1432568932',
                    '-33.33',
                    'MUHAMMAD.RIZALDY',
                    CURRENT_TIMESTAMP
                );
            
                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = '015',
                    COA_CODE = '6201010401',
                    ACT_JAN = '0',
                    ACT_FEB = '0',
                    ACT_MAR = '0',
                    ACT_APR = '0',
                    ACT_MAY = '0',
                    ACT_JUN = '0',
                    ACT_JUL = '0',
                    ACT_AUG = '0',
                    OUTLOOK_SEP = '0',
                    OUTLOOK_OCT = '0',
                    OUTLOOK_NOV = '12000000',
                    OUTLOOK_DEC = '0',
                    ADJ = '0',
                    YTD_ACTUAL_ADJ = '0',
                    OUTLOOK = '12000000',
                    LATEST_PERIOD = '12000000',
                    ANNUALIZED_YTD = '0',
                    VARIANCE_RP = '12000000',
                    VARIANCE_PERSEN = '0',
                    UPDATE_USER = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY')
                    AND CC_CODE = '015' 
                    AND COA_CODE = '6201010401'
                ;
            COMMIT;
END : 2018-09-10 21:01:50
