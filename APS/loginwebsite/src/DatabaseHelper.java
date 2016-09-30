import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class DatabaseHelper
{
    /* Database login */
    private static final String DB_USER = "root";
    private static final String DB_PWD = null;

    /* Database URLs */
    public static final String DB_URL_BASE = "jdbc:mysql://localhost:3306/";
    public static final String DB_URL_BANK = DB_URL_BASE + "bank";
    public static final String DB_URL_VOUCHER = DB_URL_BASE + "voucher";
    public static final String DB_URL_USER = DB_URL_BASE + "user";

    /* Table names */
    public static final String BANK_KEYS_TBL = "bank_keys";
    public static final String BANK_CUSTOMERS_TBL = "bank_customers";
    public static final String VOUCHERS_TBL = "vouchers";
    public static final String SPENT_VOUCHERS_TBL = "spent_vouchers";

    /* Column names */
    public static final String BANK_KEYS_COL_PUB_EXP = "pub_exponent";
    public static final String BANK_KEYS_COL_PUB_MOD = "pub_modulus";
    public static final String BANK_KEYS_COL_PRIV_EXP = "priv_exponent";
    public static final String BANK_KEYS_COL_PRIV_MOD = "priv_modulus";

    public static final String BANK_CUSTOMERS_COL_ACC_NO = "acc_no";
    public static final String BANK_CUSTOMERS_COL_BAL = "balance";
    public static final String BANK_CUSTOMERS_COL_NAME = "name";
    public static final String BANK_CUSTOMERS_COL_PWD = "password";

    public static final String VOUCHERS_COL_ID = "voucher_id";
    public static final String VOUCHERS_COL_SIG = "voucher_signature";

    public static final String SPENT_VOUCHERS_COL_ID = "voucher_id";

    /**
     * Attempts to establish a connection to the given database URL using the
     * user and password stored in the {@link DatabaseHelper} class.
     *
     * @param url
     *            A database url of the form jdbc:subprotocol:subname
     * @return A connection to the URL
     * @throws SQLException
     *             See {@link java.sql.DriverManager#getConnection()
     *             DriverManager.getConnection()}
     */
    public static Connection getConnection(String url) throws SQLException
    {
        return DriverManager.getConnection(url, DB_USER, DB_PWD);
    }
}
