START : 2018-08-30 23:11:20

                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00003',
                    CC_CODE = '015',
                    GROUP_BUDGET = 'OPEX',
                    OUTLOOK = '218066750',
                    NEXT_BUDGET = '102058576',
                    VAR_SELISIH = '-116008174',
                    VAR_PERSEN = '-53.19',
                    INSERT_USER = 'NICHOLAS.BUDIHARDJA',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00003'
                    AND CC_CODE = '015'
                    AND GROUP_BUDGET = 'OPEX'
            
                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00003',
                    CC_CODE = '015',
                    GROUP_BUDGET = 'CAPEX',
                    OUTLOOK = '0',
                    NEXT_BUDGET = '107058576',
                    VAR_SELISIH = '107058576',
                    VAR_PERSEN = '0',
                    INSERT_USER = 'NICHOLAS.BUDIHARDJA',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00003'
                    AND CC_CODE = '015'
                    AND GROUP_BUDGET = 'CAPEX'
            
                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00003',
                    CC_CODE = '015',
                    GROUP_BUDGET = 'TOTAL',
                    OUTLOOK = '218066750',
                    NEXT_BUDGET = '209117152',
                    VAR_SELISIH = '-8949598',
                    VAR_PERSEN = '-53.19',
                    INSERT_USER = 'NICHOLAS.BUDIHARDJA',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00003'
                    AND CC_CODE = '015'
                    AND GROUP_BUDGET = 'TOTAL'
            COMMIT;
END : 2018-08-30 23:11:20
