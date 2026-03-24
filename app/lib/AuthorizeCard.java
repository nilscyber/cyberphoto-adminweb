import com.verifyeasy.server.VEServer.*;

public class AuthorizeCard {

	public static void main(String[] args) {

		com.verifyeasy.server.VEServer veServer =
		com.verifyeasy.server.VEServer.getInstance("https://secure.incab.se/verify/server/cyber");
		//com.verifyeasy.server.VEServer.getInstance("https://secure.incab.se/verify/server/cyber", "D:/debitech/trusted.cer", null, null);
		com.verifyeasy.server.VEServer.ReturnData returnData;

		try {
			returnData = veServer.authorize (
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
				"",
				"SEK",
				"001",
				"");

			System.out.println ("Result code:"
			+ returnData.getResultCode()+
					", verify ID:"
			+returnData.getVerifyID());

		}
		catch (java.io.IOException e) {
			System.out.println(e);
		}


	}
}