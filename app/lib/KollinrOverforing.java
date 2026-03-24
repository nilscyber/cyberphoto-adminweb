import com.verifyeasy.server.VEServer.*;
import java.sql.*;
import javax.sql.*;
import java.util.GregorianCalendar;
import java.util.Calendar;
import java.util.Date;
import java.text.*;

public class KollinrOverforing {
	private static Connection con = null;

	public static com.verifyeasy.server.VEServer.ReturnData returnData;
	public static com.verifyeasy.server.VEServer veServer;
	public static int resultCode;
	public static long verifyID;

	public KollinrOverforing () {

	}

	public static void main(String[] args) {

		//System.out.println (args[0] + "2 " + args[1] + "3 " + args[2] + "4 "+args[3]+"5 "+args[4]+"6 "+args[5]+"7 "+args[6]+"8 "+args[7]+"9 "+args[8]+"10 "+args[9]+"11 "+args[10]+"12 "+args[11]+"13 "+args[12]+"14 "+args[13]);
		//System.exit(0);
		// Ladda sql drivrutin först
		try { // load the driver
			Class.forName("com.microsoft.jdbc.sqlserver.SQLServerDriver");
		}
		catch( Exception e ) {
				//e.printStackTrace();
				System.out.println("Failed to load driver");
				//return;
		}
		try {
			String url = "jdbc:microsoft:sqlserver://212.217.249.222:1433;DatabaseName=avanzo";
			con = DriverManager.getConnection(url, "nilshem", "Hb#3ksg");
		}
		catch ( Exception e ) {
			System.out.println (e);
			System.exit(0);
		}


		KollinrOverforing kollinr = new KollinrOverforing();
		kollinr.overforKollinr();

	}


	public static void overforKollinr () {
		String s;
		Date today;
		String idag;
		SimpleDateFormat formatter;
		formatter = new SimpleDateFormat("yyyy-MM-dd");
		today = new Date();
		idag = formatter.format(today);
		try {

			s = "SELECT prc.ParcelNo, Ordertabell.ordernr, Ordertabell.skickat FROM prc, psl, Ordertabell WHERE Ordertabell.ordernr = psl.OrderNo AND psl.idPSL = prc.idPSL AND skickat = '" + idag + "'";
			Statement select = con.createStatement();
			ResultSet result = select.executeQuery
				(s);

			// flytta till första positionen
			//result.next();
			while (result.next()) {
				System.out.println(result.getString("ordernr") + " " + result.getDate("skickat") + " " + result.getString("ParcelNo"));
			}

		}

		catch ( Exception e) {
			System.out.println (e);
		}


	}
	public static long getOrdernr () {

			long maxNr;
			maxNr = 0;



			try {

					Statement select = con.createStatement();
					ResultSet result = select.executeQuery
						("SELECT max(ordernr) as maxNr FROM Ordertabell ");

					// flytta till första positionen
					result.next();
					maxNr = result.getLong("maxNr");
					if (result.wasNull() ) {
							maxNr = 0;
					}
					else {

						String strInsert;
						String strDelete;
						//strDelete = "DELETE FROM Ordertabell WHERE ordernr = 62516";
						maxNr += 1;
						strInsert = "INSERT INTO Ordertabell (ordernr) values (" + maxNr + ")";
						try {
							//System.out.println(strInsert);
							Statement insertStmt = con.createStatement();
							insertStmt.executeUpdate (strInsert);
						}
						catch ( Exception e) {
							maxNr = 0;
						}

					}

					//System.out.println("ordernummer = " + maxNr);

				}

				catch( Exception e ) {
						//e.printStackTrace();
						maxNr = 0;
						//System.out.println ( e );
				}
			return maxNr;

		}


}