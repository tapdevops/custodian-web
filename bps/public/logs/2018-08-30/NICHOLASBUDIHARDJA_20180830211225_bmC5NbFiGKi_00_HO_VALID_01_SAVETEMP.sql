START : 2018-08-30 21:12:25

                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00002',
                    CC_CODE = '005',
                    GROUP_BUDGET = 'OPEX',
                    OUTLOOK = '1002017040',
                    NEXT_BUDGET = '0',
                    VAR_SELISIH = '-1002017040',
                    VAR_PERSEN = '0',
                    INSERT_USER = 'NICHOLAS.BUDIHARDJA',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00002'
                    AND CC_CODE = '005'
                    AND GROUP_BUDGET = 'OPEX'
            
                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00002',
                    CC_CODE = '005',
                    GROUP_BUDGET = 'CAPEX',
                    OUTLOOK = '0',
                    NEXT_BUDGET = '21642752',
                    VAR_SELISIH = '21642752',
                    VAR_PERSEN = '0',
                    INSERT_USER = 'NICHOLAS.BUDIHARDJA',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00002'
                    AND CC_CODE = '005'
                    AND GROUP_BUDGET = 'CAPEX'
            
                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00002',
                    CC_CODE = '005',
                    GROUP_BUDGET = 'TOTAL',
                    OUTLOOK = '1002017040',
                    NEXT_BUDGET = '21642752',
                    VAR_SELISIH = '-980374288',
                    VAR_PERSEN = '0',
                    INSERT_USER = 'NICHOLAS.BUDIHARDJA',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00002'
                    AND CC_CODE = '005'
                    AND GROUP_BUDGET = 'TOTAL'
            COMMIT;
END : 2018-08-30 21:12:25
