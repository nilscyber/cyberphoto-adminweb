import com.verifyeasy.server.VEServer.*;
import java.sql.*;
import javax.sql.*;

public class DebiTech_test {
	private static Connection con = null;

	public static com.verifyeasy.server.VEServer.ReturnData returnData;
	public static com.verifyeasy.server.VEServer veServer;
	public static int resultCode;
	public static long verifyID;

	public DebiTech_test() {

	}

	public static void main(String[] args) {

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
			String url = "jdbc:microsoft:sqlserver://212.217.249.222:1433";
			con = DriverManager.getConnection(url, "nilshem", "Hb#3ksg");
		}
		catch ( Exception e ) {
			System.out.println (e);
			System.exit(0);
		}

		long ordernr = 0;
		ordernr = getOrdernr();

		//ordernr = 0;

		if (ordernr == 0) {
			System.out.println("0:999");
			System.exit(0);
		}


		DebiTech_test debitech = new DebiTech_test();
		/*

		debitech.authorizeCard (
				"Nils",
				"Kohlström",
				"Åkervägen 10",
				"Umeå",
				"SE",
				"4263870056359946",
				8,
				4,
				"nils@cyberphoto.se",
				"212.217.249.194",
				"1:vara:1:1:",
				"SEK",
				"001",
				"&uses3dsecure=yes&securityCode=123&Test=1");
		*/
		debitech.authorizeCard (
				args[0], // förnamn
				args[1], // efternamn
				args[2], // adress
				args[3], // stad
				args[4], // land
				args[5], // kortnummer
				Integer.parseInt(args[6]), // månad
				Integer.parseInt(args[7]), // år
				args[8], // mail adress
				args[9], // ip nummer
				args[10], // data sträng
				args[11], // valuta (SEK)
				Long.toString(ordernr), // vår referens, ordernr
				args[13]); // extra

		//*/
		System.out.println(ordernr + ":100");
		String strUpdate;
		strUpdate = "UPDATE Ordertabell SET keys = 999 WHERE ordernr = " + ordernr;
		try {
			Statement updateStmt = con.createStatement();
			updateStmt.executeUpdate (strUpdate);
		}
		catch ( Exception e ) {
			System.out.println(e);
		}



		System.exit(0);
		resultCode = returnData.getResultCode();
		verifyID = returnData.getVerifyID();

		if (resultCode == 100) {

			System.out.println (ordernr + ":" + verifyID);
			strUpdate = "UPDATE Ordertabell SET keys = " + verifyID + " WHERE ordernr = " + ordernr;

		}
		else {
			System.out.println ("0:"+resultCode);
			strUpdate = "DELETE FROM Ordertabell WHERE ordernr = " + ordernr;
			//System.out.println(strUpdate);
		}



	}

	public void authorizeCard(String firstName, String lastName, String address, String city, String country,
								String cc, int expM, int expY, String eMail, String ip, String data,
								String currency, String ordernr, String extra) {

		veServer =
		com.verifyeasy.server.VEServer.getInstance("https://secure.incab.se/verify/server/cyber");
		//com.verifyeasy.server.VEServer.getInstance("https://secure.incab.se/verify/server/cyber", "D:/debitech/trusted.cer", null, null);

		try {
			returnData = veServer.authorize (
				firstName,
				lastName,
				address,
				city,
				country,
				cc,
				expM,
				expY,
				eMail,
				ip,
				data,
				currency,
				ordernr,
				extra);

		}
		catch (java.io.IOException e) {
			System.out.println(e);
		}


	}

	public static boolean checkCard (String cardNo) {

		com.verifyeasy.server.VEServer veServer =
		com.verifyeasy.server.VEServer.getInstance("https://secure.incab.se/verify/server/cyber");
		//boolean s;
		//s = veServer.checkSwedishPersNo("6906278673");
		//private boolean
		//s = veServer.checkCCNo(cardNo);

		return veServer.checkCCNo(cardNo);


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
				}
			return maxNr;

		}


}