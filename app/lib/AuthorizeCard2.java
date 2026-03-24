import com.verifyeasy.server.VEServer.*;

public class AuthorizeCard2 {

	public static void main(String[] args) {

		//System.out.println(checkCard("4263870056359946"));
		System.out.println(checkCard(args[0]));


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


}