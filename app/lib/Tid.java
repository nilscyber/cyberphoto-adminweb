import java.util.GregorianCalendar;
import java.util.Calendar;
import java.util.Date;
import java.text.*;
//import SimpleTimeZone.*;
import java.util.*;
import SimpleDateFormat.*;
/*
 Symbol   Meaning                 Presentation        Example
 ------   -------                 ------------        -------
 G        era designator          (Text)              AD
 y        year                    (Number)            1996
 M        month in year           (Text & Number)     July & 07
 d        day in month            (Number)            10
 h        hour in am/pm (1~12)    (Number)            12
 H        hour in day (0~23)      (Number)            0
 m        minute in hour          (Number)            30
 s        second in minute        (Number)            55
 S        millisecond             (Number)            978
 E        day in week             (Text)              Tuesday
 D        day in year             (Number)            189
 F        day of week in month    (Number)            2 (2nd Wed in July)
 w        week in year            (Number)            27
 W        week in month           (Number)            2
 a        am/pm marker            (Text)              PM
 k        hour in day (1~24)      (Number)            24
 K        hour in am/pm (0~11)    (Number)            0
 z        time zone               (Text)              Pacific Standard Time
 '        escape for text         (Delimiter)
 ''       single quote            (Literal)           '
*/

public class Tid {

	public static void main(String[] args) {
		System.out.println(nu());
	}
	public static String test() {
	  Date now = new Date();

	  DateFormat df = DateFormat.getDateInstance(DateFormat.LONG);
	  String s = df.format(now);
	  return s;
	  //System.out.println("Today is " + s);
	}
	public static String nu(){

		Date today;
		String idag;
		SimpleDateFormat formatter;
		Locale loc = Locale.getDefault();
		formatter = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", loc);
		today = new Date();
		idag = formatter.format(today);
		return idag;
		/*
		      Date now = new Date();
		      long nowLong = now.getTime();
		      return nowLong;
		      //System.out.println("Value is " + nowLong);
		*/

	}
	public static String datum() {
		Date today;
		String idag;
		SimpleDateFormat formatter;
		formatter = new SimpleDateFormat("yyyy-MM-dd");
		today = new Date();
		idag = formatter.format(today);
		return idag;
	}

}