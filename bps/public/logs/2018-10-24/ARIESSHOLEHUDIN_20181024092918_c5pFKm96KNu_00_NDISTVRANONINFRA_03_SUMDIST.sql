START : 2018-10-24 09:29:18

			DELETE FROM TR_RKT_VRA_DISTRIBUSI_SUM
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-2018'
				AND BA_CODE IN ('2121')
				AND VRA_CODE IN ('AB010')
				AND ACTIVITY_CODE IN ('5201011899') 
				AND TIPE_TRANSAKSI = 'NON_INFRA';
		
				INSERT INTO TR_RKT_VRA_DISTRIBUSI_SUM (PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, TOTAL_HM_KM, TOTAL_PRICE_HM_KM, TIPE_TRANSAKSI, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('01-01-2018','DD-MM-RRRR'),
					'2121',
					'5201011899',
					'AB010',
					'15',
					'43054.407',
					'NON_INFRA',
					'ARIES.SHOLEHUDIN',
					SYSDATE
				);
			COMMIT;
END : 2018-10-24 09:29:18
