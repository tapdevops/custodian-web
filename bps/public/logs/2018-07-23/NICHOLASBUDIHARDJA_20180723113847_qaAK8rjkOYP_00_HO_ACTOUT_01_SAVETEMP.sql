START : 2018-07-23 11:38:48

                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = 'D00057',
                    COA_CODE = '6201011101',
                    ACT_JAN = '34314100',
                    ACT_FEB = '11139600',
                    ACT_MAR = '4874500',
                    ACT_APR = '4010500',
                    ACT_MAY = '500',
                    ACT_JUN = '226000500',
                    ACT_JUL = '500',
                    ACT_AUG = '32532140',
                    OUTLOOK_SEP = '50',
                    OUTLOOK_OCT = '50',
                    OUTLOOK_NOV = '50',
                    OUTLOOK_DEC = '50',
                    ADJ = '7300',
                    YTD_ACTUAL_ADJ = '312879640',
                    OUTLOOK = '200',
                    LATEST_PERIOD = '312872540',
                    ANNUALIZED_YTD = '469308510',
                    VARIANCE_RP = '-156435970',
                    VARIANCE_PERSEN = '-33.33',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = '2018' 
                    AND CC_CODE = 'D00057' 
                    AND COA_CODE = '6201011101'
                ;
            
                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = 'D00057',
                    COA_CODE = '6201010102',
                    ACT_JAN = '31968000',
                    ACT_FEB = '7200900',
                    ACT_MAR = '0',
                    ACT_APR = '2900000',
                    ACT_MAY = '0',
                    ACT_JUN = '226000000',
                    ACT_JUL = '0',
                    ACT_AUG = '32531640',
                    OUTLOOK_SEP = '0',
                    OUTLOOK_OCT = '0',
                    OUTLOOK_NOV = '0',
                    OUTLOOK_DEC = '0',
                    ADJ = '0',
                    YTD_ACTUAL_ADJ = '300600540',
                    OUTLOOK = '0',
                    LATEST_PERIOD = '300600540',
                    ANNUALIZED_YTD = '450900810',
                    VARIANCE_RP = '-150300270',
                    VARIANCE_PERSEN = '-33.33',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = '2018' 
                    AND CC_CODE = 'D00057' 
                    AND COA_CODE = '6201010102'
                ;
            COMMIT;
END : 2018-07-23 11:38:48
