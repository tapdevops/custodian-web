START : 2018-08-29 15:30:11

            UPDATE TM_HO_COST_CENTER
            SET DELETE_USER = 'MUHAMMAD.RIZALDY',
                DELETE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3bAAeAAAxSPAAy';
        COMMIT;
END : 2018-08-29 15:30:11