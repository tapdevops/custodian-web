START : 2018-09-25 14:34:27

                UPDATE TR_HO_SPD 
                SET 
                    PERIOD_BUDGET   = TO_DATE('2018', 'YYYY'),
                    DIV_CODE        = '',
                    CC_CODE         = '015',
                    RK_ID           = '234',
                    SPD_DESCRIPTION = 'SO Kaltim 1',
                    COA_CODE        = '6201010401',
                    NORMA_SPD_ID    = '92',
                    CORE_CODE       = 'HO',
                    COMP_CODE       = '54',
                    BA_CODE         = '5411',
                    PLAN            = '3',
                    GOLONGAN        = '5',
                    JLH_PRIA        = '1',
                    JLH_WANITA      = '',
                    JLH_HARI        = '3',
                    TIKET           = '0',
                    TRANSPORT_LAIN  = '0',
                    UANG_MAKAN      = '240000',
                    UANG_SAKU       = '135000',
                    HOTEL_JLH_HARI  = '0',
                    HOTEL_JLH_TARIF = '0',
                    OTHERS          = '0',
                    TOTAL           = '0',
                    REMARKS_OTHERS  = '',
                    SEBARAN_JAN     = '0',
                    SEBARAN_FEB     = '0',
                    SEBARAN_MAR     = '0',
                    SEBARAN_APR     = '0',
                    SEBARAN_MAY     = '0',
                    SEBARAN_JUN     = '0',
                    SEBARAN_JUL     = '0',
                    SEBARAN_AUG     = '0',
                    SEBARAN_SEP     = '0',
                    SEBARAN_OCT     = '0',
                    SEBARAN_NOV     = '0',
                    SEBARAN_DEC     = '0',
                    SEBARAN_TOTAL   = '0',
                    TIPE_NORMA      = 'UMUM',
                    UPDATE_USER     = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME     = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAsHlAAaAACalcAAG';
            COMMIT;
END : 2018-09-25 14:34:27
