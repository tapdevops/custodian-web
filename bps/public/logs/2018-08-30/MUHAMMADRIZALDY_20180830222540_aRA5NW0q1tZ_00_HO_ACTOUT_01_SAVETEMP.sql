START : 2018-08-30 22:25:40

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
                    '6201010601',
                    '8750000',
                    '8750000',
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
                    '17500000',
                    '0',
                    '17500000',
                    '26250000',
                    '-8750000',
                    '-33.33',
                    'MUHAMMAD.RIZALDY',
                    CURRENT_TIMESTAMP
                );
            
                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = '015',
                    COA_CODE = '6201010603',
                    ACT_JAN = '0',
                    ACT_FEB = '0',
                    ACT_MAR = '0',
                    ACT_APR = '0',
                    ACT_MAY = '0',
                    ACT_JUN = '249375',
                    ACT_JUL = '249375',
                    ACT_AUG = '0',
                    OUTLOOK_SEP = '0',
                    OUTLOOK_OCT = '0',
                    OUTLOOK_NOV = '0',
                    OUTLOOK_DEC = '200000000',
                    ADJ = '0',
                    YTD_ACTUAL_ADJ = '498750',
                    OUTLOOK = '200000000',
                    LATEST_PERIOD = '200498750',
                    ANNUALIZED_YTD = '748125',
                    VARIANCE_RP = '199750625',
                    VARIANCE_PERSEN = '26700.17',
                    UPDATE_USER = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY')
                    AND CC_CODE = '015' 
                    AND COA_CODE = '6201010603'
                ;
            COMMIT;
END : 2018-08-30 22:25:40
