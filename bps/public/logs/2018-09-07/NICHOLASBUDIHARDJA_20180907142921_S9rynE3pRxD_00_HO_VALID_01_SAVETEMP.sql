START : 2018-09-07 14:29:21

                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00002',
                    CC_CODE = '005',
                    GROUP_BUDGET = 'OPEX',
                    OUTLOOK = '1541284901',
                    NEXT_BUDGET = '3494605',
                    VAR_SELISIH = '-1537790296',
                    VAR_PERSEN = '-99.77',
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
                    NEXT_BUDGET = '25137357',
                    VAR_SELISIH = '25137357',
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
                    OUTLOOK = '1541284901',
                    NEXT_BUDGET = '28631962',
                    VAR_SELISIH = '-1512652939',
                    VAR_PERSEN = '-99.77',
                    INSERT_USER = 'NICHOLAS.BUDIHARDJA',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00002'
                    AND CC_CODE = '005'
                    AND GROUP_BUDGET = 'TOTAL'
            COMMIT;
END : 2018-09-07 14:29:24
