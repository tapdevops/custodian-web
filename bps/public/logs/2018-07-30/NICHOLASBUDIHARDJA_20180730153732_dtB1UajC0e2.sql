START : 2018-07-30 15:37:32

            UPDATE TM_HO_COA
            SET DELETE_USER = 'NICHOLAS.BUDIHARDJA',
                DELETE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3pAAeAAAxS7AAA';
        COMMIT;
END : 2018-07-30 15:37:33