START : 2018-08-24 09:48:11

            UPDATE TM_HO_RENCANA_KERJA
            SET DELETE_USER = 'NICHOLAS.BUDIHARDJA',
                DELETE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr7IAAeAAAxTuAAE';
        COMMIT;
END : 2018-08-24 09:48:11
