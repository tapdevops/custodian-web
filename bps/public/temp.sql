
			SPOOL D:/run.log;
			SET ECHO ON; 
			SET TIMING ON;
			SET SERVEROUTPUT ON SIZE 30000;
			DECLARE
				success_flag BOOLEAN;
			BEGIN
				BEGIN
					DELETE FROM TR_RKT_LC_COST_ELEMENT
					WHERE PERIOD_BUDGET = TO_DATE('01-01-2014','DD-MM-RRRR') 
						AND BA_CODE = '3122A'
						AND NVL(ACTIVITY_CODE ,'--') = '10200' 
						AND NVL(ACTIVITY_CLASS,'--')  = 'ALL'
						AND NVL(AFD_CODE ,'--') = 'O' 
						AND NVL(LAND_TYPE,'--')  = 'GAMBUT'
						AND NVL(TOPOGRAPHY ,'--') = 'DATAR' 
						AND NVL(SUMBER_BIAYA,'--')  = 'EXTERNAL'
						AND COST_ELEMENT  = 'LABOUR';
					success_flag := TRUE;
				EXCEPTION
					WHEN OTHERS THEN 
						raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
						success_flag := FALSE;
				END;
				
				IF success_flag THEN
					insertLog('UPDATE SUCCESS', 'OER BA', 'YOPIE.IRAWAN', '', '10.20.1.254');
				ELSE
					insertLog('UPDATE FAILED', 'OER BA', 'YOPIE.IRAWAN', SQLCODE, '10.20.1.254');
				END IF;
			END;
				DECLARE
					success_flag BOOLEAN;
				BEGIN
					BEGIN
						INSERT INTO TR_RKT_LC_COST_ELEMENT (
						   PERIOD_BUDGET, BA_CODE, AFD_CODE, 
						   ACTIVITY_CODE, ACTIVITY_CLASS, COST_ELEMENT, 
						   LAND_TYPE, TOPOGRAPHY,
						   TRX_RKT_CODE, SUMBER_BIAYA, DIS_COST_JAN, 
						   DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, 
						   DIS_COST_MAY, DIS_COST_JUN, DIS_COST_JUL, 
						   DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, 
						   DIS_COST_NOV, DIS_COST_DEC, DIS_COST_SETAHUN,PLAN_JAN, 
						   PLAN_FEB, PLAN_MAR, PLAN_APR, 
						   PLAN_MAY, PLAN_JUN, PLAN_JUL, 
						   PLAN_AUG, PLAN_SEP, PLAN_OCT, 
						   PLAN_NOV, PLAN_DEC, PLAN_SETAHUN, 
						   INSERT_USER, INSERT_TIME, TOTAL_RP_QTY, COST_SMS1, COST_SMS2) 
						VALUES (
						   TO_DATE('01-01-2014','DD-MM-RRRR'), '3122A', UPPER('O'),
						   '10200', 'ALL', 'LABOUR',
						   'GAMBUT', 'DATAR', 
						   F_GEN_TRANSACTION_CODE(TO_DATE('01-01-2014','DD-MM-RRRR'),TRIM('3122A'),'LC'), 'EXTERNAL',
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0), 
							(0),
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							13.58, 
							13.58,
							'YOPIE.IRAWAN',  
							SYSDATE,
							0, 
							0, 
							0);
						success_flag := TRUE;
					EXCEPTION
						WHEN OTHERS THEN 
							raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
							success_flag := FALSE;
					END;
					
					IF success_flag THEN
						insertLog('UPDATE SUCCESS', 'OER BA', 'YOPIE.IRAWAN', '', '10.20.1.254');
					ELSE
						insertLog('UPDATE FAILED', 'OER BA', 'YOPIE.IRAWAN', SQLCODE, '10.20.1.254');
					END IF;
				END;
			DECLARE
				success_flag BOOLEAN;
			BEGIN
				BEGIN
					DELETE FROM TR_RKT_LC_COST_ELEMENT
					WHERE PERIOD_BUDGET = TO_DATE('01-01-2014','DD-MM-RRRR') 
						AND BA_CODE = '3122A'
						AND NVL(ACTIVITY_CODE ,'--') = '10200' 
						AND NVL(ACTIVITY_CLASS,'--')  = 'ALL'
						AND NVL(AFD_CODE ,'--') = 'O' 
						AND NVL(LAND_TYPE,'--')  = 'GAMBUT'
						AND NVL(TOPOGRAPHY ,'--') = 'DATAR' 
						AND NVL(SUMBER_BIAYA,'--')  = 'EXTERNAL'
						AND COST_ELEMENT  = 'MATERIAL';
					success_flag := TRUE;
				EXCEPTION
					WHEN OTHERS THEN 
						raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
						success_flag := FALSE;
				END;
				
				IF success_flag THEN
					insertLog('UPDATE SUCCESS', 'OER BA', 'YOPIE.IRAWAN', '', '10.20.1.254');
				ELSE
					insertLog('UPDATE FAILED', 'OER BA', 'YOPIE.IRAWAN', SQLCODE, '10.20.1.254');
				END IF;
			END;
				DECLARE
					success_flag BOOLEAN;
				BEGIN
					BEGIN
						INSERT INTO TR_RKT_LC_COST_ELEMENT (
						   PERIOD_BUDGET, BA_CODE, AFD_CODE, 
						   ACTIVITY_CODE, ACTIVITY_CLASS, COST_ELEMENT, 
						   LAND_TYPE, TOPOGRAPHY,
						   TRX_RKT_CODE, SUMBER_BIAYA, DIS_COST_JAN, 
						   DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, 
						   DIS_COST_MAY, DIS_COST_JUN, DIS_COST_JUL, 
						   DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, 
						   DIS_COST_NOV, DIS_COST_DEC, DIS_COST_SETAHUN,PLAN_JAN, 
						   PLAN_FEB, PLAN_MAR, PLAN_APR, 
						   PLAN_MAY, PLAN_JUN, PLAN_JUL, 
						   PLAN_AUG, PLAN_SEP, PLAN_OCT, 
						   PLAN_NOV, PLAN_DEC, PLAN_SETAHUN, 
						   INSERT_USER, INSERT_TIME, TOTAL_RP_QTY, COST_SMS1, COST_SMS2) 
						VALUES (
						   TO_DATE('01-01-2014','DD-MM-RRRR'), '3122A', UPPER('O'),
						   '10200', 'ALL', 'MATERIAL',
						   'GAMBUT', 'DATAR', 
						   F_GEN_TRANSACTION_CODE(TO_DATE('01-01-2014','DD-MM-RRRR'),TRIM('3122A'),'LC'), 'EXTERNAL',
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0), 
							(0),
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							13.58, 
							13.58,
							'YOPIE.IRAWAN',  
							SYSDATE,
							0, 
							0, 
							0);
						success_flag := TRUE;
					EXCEPTION
						WHEN OTHERS THEN 
							raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
							success_flag := FALSE;
					END;
					
					IF success_flag THEN
						insertLog('UPDATE SUCCESS', 'OER BA', 'YOPIE.IRAWAN', '', '10.20.1.254');
					ELSE
						insertLog('UPDATE FAILED', 'OER BA', 'YOPIE.IRAWAN', SQLCODE, '10.20.1.254');
					END IF;
				END;
			DECLARE
				success_flag BOOLEAN;
			BEGIN
				BEGIN
					DELETE FROM TR_RKT_LC_COST_ELEMENT
					WHERE PERIOD_BUDGET = TO_DATE('01-01-2014','DD-MM-RRRR') 
						AND BA_CODE = '3122A'
						AND NVL(ACTIVITY_CODE ,'--') = '10200' 
						AND NVL(ACTIVITY_CLASS,'--')  = 'ALL'
						AND NVL(AFD_CODE ,'--') = 'O' 
						AND NVL(LAND_TYPE,'--')  = 'GAMBUT'
						AND NVL(TOPOGRAPHY ,'--') = 'DATAR' 
						AND NVL(SUMBER_BIAYA,'--')  = 'EXTERNAL'
						AND COST_ELEMENT  = 'TOOLS';
					success_flag := TRUE;
				EXCEPTION
					WHEN OTHERS THEN 
						raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
						success_flag := FALSE;
				END;
				
				IF success_flag THEN
					insertLog('UPDATE SUCCESS', 'OER BA', 'YOPIE.IRAWAN', '', '10.20.1.254');
				ELSE
					insertLog('UPDATE FAILED', 'OER BA', 'YOPIE.IRAWAN', SQLCODE, '10.20.1.254');
				END IF;
			END;
				DECLARE
					success_flag BOOLEAN;
				BEGIN
					BEGIN
						INSERT INTO TR_RKT_LC_COST_ELEMENT (
						   PERIOD_BUDGET, BA_CODE, AFD_CODE, 
						   ACTIVITY_CODE, ACTIVITY_CLASS, COST_ELEMENT, 
						   LAND_TYPE, TOPOGRAPHY,
						   TRX_RKT_CODE, SUMBER_BIAYA, DIS_COST_JAN, 
						   DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, 
						   DIS_COST_MAY, DIS_COST_JUN, DIS_COST_JUL, 
						   DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, 
						   DIS_COST_NOV, DIS_COST_DEC, DIS_COST_SETAHUN,PLAN_JAN, 
						   PLAN_FEB, PLAN_MAR, PLAN_APR, 
						   PLAN_MAY, PLAN_JUN, PLAN_JUL, 
						   PLAN_AUG, PLAN_SEP, PLAN_OCT, 
						   PLAN_NOV, PLAN_DEC, PLAN_SETAHUN, 
						   INSERT_USER, INSERT_TIME, TOTAL_RP_QTY, COST_SMS1, COST_SMS2) 
						VALUES (
						   TO_DATE('01-01-2014','DD-MM-RRRR'), '3122A', UPPER('O'),
						   '10200', 'ALL', 'TOOLS',
						   'GAMBUT', 'DATAR', 
						   F_GEN_TRANSACTION_CODE(TO_DATE('01-01-2014','DD-MM-RRRR'),TRIM('3122A'),'LC'), 'EXTERNAL',
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0), 
							(0),
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							13.58, 
							13.58,
							'YOPIE.IRAWAN',  
							SYSDATE,
							0, 
							0, 
							0);
						success_flag := TRUE;
					EXCEPTION
						WHEN OTHERS THEN 
							raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
							success_flag := FALSE;
					END;
					
					IF success_flag THEN
						insertLog('UPDATE SUCCESS', 'OER BA', 'YOPIE.IRAWAN', '', '10.20.1.254');
					ELSE
						insertLog('UPDATE FAILED', 'OER BA', 'YOPIE.IRAWAN', SQLCODE, '10.20.1.254');
					END IF;
				END;
			DECLARE
				success_flag BOOLEAN;
			BEGIN
				BEGIN
					DELETE FROM TR_RKT_LC_COST_ELEMENT
					WHERE PERIOD_BUDGET = TO_DATE('01-01-2014','DD-MM-RRRR') 
						AND BA_CODE = '3122A'
						AND NVL(ACTIVITY_CODE ,'--') = '10200' 
						AND NVL(ACTIVITY_CLASS,'--')  = 'ALL'
						AND NVL(AFD_CODE ,'--') = 'O' 
						AND NVL(LAND_TYPE,'--')  = 'GAMBUT'
						AND NVL(TOPOGRAPHY ,'--') = 'DATAR' 
						AND NVL(SUMBER_BIAYA,'--')  = 'EXTERNAL'
						AND COST_ELEMENT  = 'TRANSPORT';
					success_flag := TRUE;
				EXCEPTION
					WHEN OTHERS THEN 
						raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
						success_flag := FALSE;
				END;
				
				IF success_flag THEN
					insertLog('UPDATE SUCCESS', 'OER BA', 'YOPIE.IRAWAN', '', '10.20.1.254');
				ELSE
					insertLog('UPDATE FAILED', 'OER BA', 'YOPIE.IRAWAN', SQLCODE, '10.20.1.254');
				END IF;
			END;
				DECLARE
					success_flag BOOLEAN;
				BEGIN
					BEGIN
						INSERT INTO TR_RKT_LC_COST_ELEMENT (
						   PERIOD_BUDGET, BA_CODE, AFD_CODE, 
						   ACTIVITY_CODE, ACTIVITY_CLASS, COST_ELEMENT, 
						   LAND_TYPE, TOPOGRAPHY,
						   TRX_RKT_CODE, SUMBER_BIAYA, DIS_COST_JAN, 
						   DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, 
						   DIS_COST_MAY, DIS_COST_JUN, DIS_COST_JUL, 
						   DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, 
						   DIS_COST_NOV, DIS_COST_DEC, DIS_COST_SETAHUN,PLAN_JAN, 
						   PLAN_FEB, PLAN_MAR, PLAN_APR, 
						   PLAN_MAY, PLAN_JUN, PLAN_JUL, 
						   PLAN_AUG, PLAN_SEP, PLAN_OCT, 
						   PLAN_NOV, PLAN_DEC, PLAN_SETAHUN, 
						   INSERT_USER, INSERT_TIME, TOTAL_RP_QTY, COST_SMS1, COST_SMS2) 
						VALUES (
						   TO_DATE('01-01-2014','DD-MM-RRRR'), '3122A', UPPER('O'),
						   '10200', 'ALL', 'TRANSPORT',
						   'GAMBUT', 'DATAR', 
						   F_GEN_TRANSACTION_CODE(TO_DATE('01-01-2014','DD-MM-RRRR'),TRIM('3122A'),'LC'), 'EXTERNAL',
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0), 
							(0),
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							13.58, 
							13.58,
							'YOPIE.IRAWAN',  
							SYSDATE,
							0, 
							0, 
							0);
						success_flag := TRUE;
					EXCEPTION
						WHEN OTHERS THEN 
							raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
							success_flag := FALSE;
					END;
					
					IF success_flag THEN
						insertLog('UPDATE SUCCESS', 'OER BA', 'YOPIE.IRAWAN', '', '10.20.1.254');
					ELSE
						insertLog('UPDATE FAILED', 'OER BA', 'YOPIE.IRAWAN', SQLCODE, '10.20.1.254');
					END IF;
				END;
			DECLARE
				success_flag BOOLEAN;
			BEGIN
				BEGIN
					DELETE FROM TR_RKT_LC_COST_ELEMENT
					WHERE PERIOD_BUDGET = TO_DATE('01-01-2014','DD-MM-RRRR') 
						AND BA_CODE = '3122A'
						AND NVL(ACTIVITY_CODE ,'--') = '10200' 
						AND NVL(ACTIVITY_CLASS,'--')  = 'ALL'
						AND NVL(AFD_CODE ,'--') = 'O' 
						AND NVL(LAND_TYPE,'--')  = 'GAMBUT'
						AND NVL(TOPOGRAPHY ,'--') = 'DATAR' 
						AND NVL(SUMBER_BIAYA,'--')  = 'EXTERNAL'
						AND COST_ELEMENT  = 'CONTRACT';
					success_flag := TRUE;
				EXCEPTION
					WHEN OTHERS THEN 
						raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
						success_flag := FALSE;
				END;
				
				IF success_flag THEN
					insertLog('UPDATE SUCCESS', 'OER BA', 'YOPIE.IRAWAN', '', '10.20.1.254');
				ELSE
					insertLog('UPDATE FAILED', 'OER BA', 'YOPIE.IRAWAN', SQLCODE, '10.20.1.254');
				END IF;
			END;
				DECLARE
					success_flag BOOLEAN;
				BEGIN
					BEGIN
						INSERT INTO TR_RKT_LC_COST_ELEMENT (
						   PERIOD_BUDGET, BA_CODE, AFD_CODE, 
						   ACTIVITY_CODE, ACTIVITY_CLASS, COST_ELEMENT, 
						   LAND_TYPE, TOPOGRAPHY,
						   TRX_RKT_CODE, SUMBER_BIAYA, DIS_COST_JAN, 
						   DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, 
						   DIS_COST_MAY, DIS_COST_JUN, DIS_COST_JUL, 
						   DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, 
						   DIS_COST_NOV, DIS_COST_DEC, DIS_COST_SETAHUN,PLAN_JAN, 
						   PLAN_FEB, PLAN_MAR, PLAN_APR, 
						   PLAN_MAY, PLAN_JUN, PLAN_JUL, 
						   PLAN_AUG, PLAN_SEP, PLAN_OCT, 
						   PLAN_NOV, PLAN_DEC, PLAN_SETAHUN, 
						   INSERT_USER, INSERT_TIME, TOTAL_RP_QTY, COST_SMS1, COST_SMS2) 
						VALUES (
						   TO_DATE('01-01-2014','DD-MM-RRRR'), '3122A', UPPER('O'),
						   '10200', 'ALL', 'CONTRACT',
						   'GAMBUT', 'DATAR', 
						   F_GEN_TRANSACTION_CODE(TO_DATE('01-01-2014','DD-MM-RRRR'),TRIM('3122A'),'LC'), 'EXTERNAL',
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(0),
							(13933080), 
							(13933080),
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							0.00,
							13.58, 
							13.58,
							'YOPIE.IRAWAN',  
							SYSDATE,
							1026000, 
							0, 
							13933080);
						success_flag := TRUE;
					EXCEPTION
						WHEN OTHERS THEN 
							raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
							success_flag := FALSE;
					END;
					
					IF success_flag THEN
						insertLog('UPDATE SUCCESS', 'OER BA', 'YOPIE.IRAWAN', '', '10.20.1.254');
					ELSE
						insertLog('UPDATE FAILED', 'OER BA', 'YOPIE.IRAWAN', SQLCODE, '10.20.1.254');
					END IF;
				END;
				DELETE FROM TR_RKT_LC
				WHERE ( 
					ROWIDTOCHAR(ROWID) = ''
					OR ROWIDTOCHAR(ROWID) = 'AAAaqMAAGAAMAYbAAN'
					OR TRX_RKT_CODE = ''
				);DELETE FROM TR_RKT_LC
				WHERE PERIOD_BUDGET = TO_DATE('01-01-2014','DD-MM-RRRR') 
					AND BA_CODE = '3122A'
					AND NVL(ACTIVITY_CODE ,'--') = '10200' 
					AND NVL(ACTIVITY_CLASS,'--')  = 'ALL'
					AND NVL(AFD_CODE ,'--') = 'O' 
					AND NVL(LAND_TYPE,'--')  = 'GAMBUT'
					AND NVL(TOPOGRAPHY ,'--') = 'DATAR' 
					AND NVL(SUMBER_BIAYA,'--')  = 'EXTERNAL';
		
			DECLARE
				success_flag BOOLEAN;
			BEGIN
				BEGIN
					INSERT INTO TR_RKT_LC (PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, 
							   AFD_CODE, LAND_TYPE, TOPOGRAPHY, 
							   ACTIVITY_CLASS, TRX_RKT_CODE, SUMBER_BIAYA, 
							   PLAN_JAN, PLAN_FEB, PLAN_MAR, 
							   PLAN_APR, PLAN_MAY, PLAN_JUN, 
							   PLAN_JUL, PLAN_AUG, PLAN_SEP, 
							   PLAN_OCT, PLAN_NOV, PLAN_DEC, 
							   PLAN_SETAHUN, COST_JAN, COST_FEB, 
							   COST_MAR, COST_APR, COST_MAY, 
							   COST_JUN, COST_JUL, COST_AUG, 
							   COST_SEP, COST_OCT, COST_NOV, 
							   COST_DEC, COST_SETAHUN,COST_SMS1,COST_SMS2, INSERT_USER, 
							   INSERT_TIME, TOTAL_RP_QTY)
					VALUES (
							TO_DATE('01-01-2014','DD-MM-RRRR'), '3122A','10200',
							   UPPER('O'), 'GAMBUT', 'DATAR', 
							   'ALL', F_GEN_TRANSACTION_CODE(TO_DATE('01-01-2014','DD-MM-RRRR'),TRIM('3122A'),'LC'), 'EXTERNAL', 
							   REPLACE('0.00',',',''), REPLACE('0.00',',',''), REPLACE('0.00',',',''), 
							   REPLACE('0.00',',',''), REPLACE('0.00',',',''), REPLACE('0.00',',',''), 
							   REPLACE('0.00',',',''), REPLACE('0.00',',',''), REPLACE('0.00',',',''), 
							   REPLACE('0.00',',',''), REPLACE('0.00',',',''), REPLACE('13.58',',',''), 
							   REPLACE('13.58',',',''), REPLACE('0',',',''), REPLACE('0',',',''), 
							   REPLACE('0',',',''), REPLACE('0',',',''), REPLACE('0',',',''), 
							   REPLACE('0',',',''), REPLACE('0',',',''), REPLACE('0',',',''), 
							   REPLACE('0',',',''), REPLACE('0',',',''), REPLACE('0',',',''), 
							   REPLACE('13933080',',',''), REPLACE('13933080',',',''), REPLACE('0',',',''), 
							   REPLACE('13933080',',',''), 'YOPIE.IRAWAN',SYSDATE, REPLACE('1026000',',',''));
					
					success_flag := TRUE;
				EXCEPTION
					WHEN OTHERS THEN 
						raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
						success_flag := FALSE;
				END;
				
				IF success_flag THEN
					insertLog('UPDATE SUCCESS', 'OER BA', 'YOPIE.IRAWAN', '', '10.20.1.254');
				ELSE
					insertLog('UPDATE FAILED', 'OER BA', 'YOPIE.IRAWAN', SQLCODE, '10.20.1.254');
				END IF;
			END;
			DELETE FROM TR_RKT_LC_TEMP
			WHERE ( 
				ROWIDTOCHAR(ROWID) = ''
				OR ROWIDTOCHAR(ROWID) = 'AAAaqMAAGAAMAYbAAN'
				OR TRX_RKT_CODE = ''
			);DELETE FROM TR_RKT_LC_TEMP
				WHERE PERIOD_BUDGET = TO_DATE('01-01-2014','DD-MM-RRRR') 
					AND BA_CODE = '3122A'
					AND NVL(ACTIVITY_CODE ,'--') = '10200' 
					AND NVL(ACTIVITY_CLASS,'--')  = 'ALL'
					AND NVL(AFD_CODE ,'--') = 'O' 
					AND NVL(LAND_TYPE,'--')  = 'GAMBUT'
					AND NVL(TOPOGRAPHY ,'--') = 'DATAR' 
					AND NVL(SUMBER_BIAYA,'--')  = 'EXTERNAL';