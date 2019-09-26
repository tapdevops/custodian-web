START : 2018-09-25 14:14:48

                UPDATE TR_HO_SPD 
                SET 
                    PERIOD_BUDGET   = TO_DATE('2018', 'YYYY'),
                    DIV_CODE        = '',
                    CC_CODE         = '015',
                    RK_ID           = '234',
                    SPD_DESCRIPTION = 'SO Kaltim 2',
                    COA_CODE        = '6201010401',
                    NORMA_SPD_ID    = '68',
                    CORE_CODE       = 'HO',
                    COMP_CODE       = '52',
                    BA_CODE         = '5211',
                    PLAN            = '6',
                    GOLONGAN        = '5',
                    JLH_PRIA        = '5',
                    JLH_WANITA      = '',
                    JLH_HARI        = '2',
                    TIKET           = '10395000',
                    TRANSPORT_LAIN  = '450000',
                    UANG_MAKAN      = '1200000',
                    UANG_SAKU       = '450000',
                    HOTEL_JLH_HARI  = '0',
                    HOTEL_JLH_TARIF = '0',
                    OTHERS          = '0',
                    TOTAL           = '12495000',
                    REMARKS_OTHERS  = '',
                    SEBARAN_JAN     = '0',
                    SEBARAN_FEB     = '0',
                    SEBARAN_MAR     = '0',
                    SEBARAN_APR     = '0',
                    SEBARAN_MAY     = '0',
                    SEBARAN_JUN     = '12495000',
                    SEBARAN_JUL     = '0',
                    SEBARAN_AUG     = '0',
                    SEBARAN_SEP     = '0',
                    SEBARAN_OCT     = '0',
                    SEBARAN_NOV     = '0',
                    SEBARAN_DEC     = '0',
                    SEBARAN_TOTAL   = '12495000',
                    TIPE_NORMA      = 'KHUSUS',
                    UPDATE_USER     = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME     = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAsHlAAaAACaleAAF';
            COMMIT;
END : 2018-09-25 14:14:49
