START : 2018-07-30 15:20:00

            UPDATE TM_HO_CORE
            SET DELETE_USER = 'NICHOLAS.BUDIHARDJA',
                DELETE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3lAAeAAAxSrAAA';
        COMMIT;
END : 2018-07-30 15:20:00