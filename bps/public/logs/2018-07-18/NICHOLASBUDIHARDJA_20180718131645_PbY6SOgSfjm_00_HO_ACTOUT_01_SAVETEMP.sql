START : 2018-07-18 13:16:45

                UPDATE TM_HO_ACT_OUTLOOK 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = 'D00057',
                    COA_CODE = '6201011101',
                    TRANSACTION_DESC = 'TESTING INSERT UPLOAD',
                    CORE_CODE = 'SITE',
                    COMPANY_CODE = '11',
                    ACT_JAN = '2,345,600.00',
                    ACT_FEB = '3,938,200.00',
                    ACT_MAR = '4,874,000.00',
                    ACT_APR = '1,110,000.00',
                    ACT_MAY = '0.00',
                    ACT_JUN = '0.00',
                    ACT_JUL = '0.00',
                    ACT_AUG = '0.00',
                    OUTLOOK_SEP = '50.00',
                    OUTLOOK_OCT = '50.00',
                    OUTLOOK_NOV = '50.00',
                    OUTLOOK_DEC = '50.00',
                    YTD_ACTUAL = '12,267,800.00',
                    ADJ = '100.00',
                    OUTLOOK = '200.00',
                    TOTAL_ACTUAL = '12,268,100.00',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr79AAeAAAxT9AAA';
            COMMIT;
END : 2018-07-18 13:16:47
