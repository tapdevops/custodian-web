START : 2018-09-25 20:31:07

                UPDATE TR_HO_SPD 
                SET 
                    PERIOD_BUDGET   = TO_DATE('2018', 'YYYY'),
                    DIV_CODE        = '',
                    CC_CODE         = '015',
                    RK_ID           = '234',
                    SPD_DESCRIPTION = 'SO Sumatera',
                    COA_CODE        = '6201010401',
                    NORMA_SPD_ID    = '65',
                    CORE_CODE       = 'HO',
                    COMP_CODE       = '21',
                    BA_CODE         = '2111',
                    PLAN            = '5',
                    GOLONGAN        = '6',
                    JLH_PRIA        = '1',
                    JLH_WANITA      = '',
                    JLH_HARI        = '2',
                    TIKET           = '2772000',
                    TRANSPORT_LAIN  = '500000',
                    UANG_MAKAN      = '0',
                    UANG_SAKU       = '0',
                    HOTEL_JLH_HARI  = '0',
                    HOTEL_JLH_TARIF = '0',
                    OTHERS          = '0',
                    TOTAL           = '3272000',
                    REMARKS_OTHERS  = '',
                    SEBARAN_JAN     = '0',
                    SEBARAN_FEB     = '0',
                    SEBARAN_MAR     = '0',
                    SEBARAN_APR     = '0',
                    SEBARAN_MAY     = '3272000',
                    SEBARAN_JUN     = '0',
                    SEBARAN_JUL     = '0',
                    SEBARAN_AUG     = '0',
                    SEBARAN_SEP     = '0',
                    SEBARAN_OCT     = '0',
                    SEBARAN_NOV     = '0',
                    SEBARAN_DEC     = '0',
                    SEBARAN_TOTAL   = '3272000',
                    TIPE_NORMA      = 'UMUM',
                    UPDATE_USER     = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME     = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAsHlAAaAACaleAAG';
            COMMIT;
END : 2018-09-25 20:31:07