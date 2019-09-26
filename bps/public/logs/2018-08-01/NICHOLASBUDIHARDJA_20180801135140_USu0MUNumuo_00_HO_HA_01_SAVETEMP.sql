START : 2018-08-01 13:51:40

            UPDATE TM_HO_HA_STATEMENT
            SET 
                EST_NAME = 'AAPA',
                TBM = '85575.00',
                TM = '105.21',
                GRAND_TOTAL = '85680.21',
                PERSEN_TBM = '99.88',
                PERSEN_TM = '0.12',
                UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3tAAeAAAxTLAAA';
        COMMIT;
END : 2018-08-01 13:51:41
