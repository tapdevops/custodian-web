START : 2018-08-24 16:53:05

                UPDATE TR_HO_SPD 
                SET 
                    PERIOD_BUDGET   = TO_DATE('2018', 'YYYY'),
                    DIV_CODE        = '',
                    CC_CODE         = 'D00057',
                    RK_ID           = '84',
                    SPD_DESCRIPTION = 'TESTING TESTING',
                    COA_CODE        = '6201011905',
                    NORMA_SPD_ID    = '42',
                    CORE_CODE       = 'SITE',
                    COMP_CODE       = '11',
                    BA_CODE         = '1121',
                    PLAN            = '8',
                    GOLONGAN        = '1',
                    JLH_PRIA        = '1',
                    JLH_WANITA      = '',
                    JLH_HARI        = '1',
                    TIKET           = '1100000',
                    TRANSPORT_LAIN  = '0',
                    UANG_MAKAN      = '0',
                    UANG_SAKU       = '0',
                    HOTEL_JLH_HARI  = '1',
                    HOTEL_JLH_TARIF = '302500',
                    OTHERS          = '50000',
                    TOTAL           = '1452500',
                    REMARKS_OTHERS  = 'TESTING TESTING',
                    SEBARAN_JAN     = '0',
                    SEBARAN_FEB     = '0',
                    SEBARAN_MAR     = '0',
                    SEBARAN_APR     = '0',
                    SEBARAN_MAY     = '0',
                    SEBARAN_JUN     = '0',
                    SEBARAN_JUL     = '0',
                    SEBARAN_AUG     = '1452500',
                    SEBARAN_SEP     = '0',
                    SEBARAN_OCT     = '0',
                    SEBARAN_NOV     = '0',
                    SEBARAN_DEC     = '0',
                    SEBARAN_TOTAL   = '1452500',
                    UPDATE_USER     = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME     = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAsHlAAaAACaleAAA';
            COMMIT;
END : 2018-08-24 16:53:05
