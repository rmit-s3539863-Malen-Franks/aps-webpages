import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.Statement;
import org.bouncycastle.util.encoders.Base64;

public class Voucher
{
    private byte[] id;
    private byte[] signature;

    /**
     * Create a {@code Voucher} from the id and signature stored in a database
     * for a
     * given voucher id.
     *
     * @param voucherId
     *            ID for the voucher
     * @return Voucher object corresponding to the id and signature stored in
     *         the database.
     */
    public static Voucher readFromDb(byte[] voucherId)
    {
        try
        {
            Connection connection = DatabaseHelper.getConnection(
                    DatabaseHelper.DB_URL_USER);
            Statement s = connection.createStatement();

            ResultSet res = s.executeQuery("select * from "
                    + DatabaseHelper.VOUCHERS_TBL
                    + " where " + DatabaseHelper.VOUCHERS_COL_ID
                    + " = \"" + Base64.toBase64String(voucherId) + "\"");

            // Move to selected row
            res.next();

            String voucherSigB64 = res.getString(
                    DatabaseHelper.VOUCHERS_COL_SIG);
            byte[] voucherSig = Base64.decode(voucherSigB64);

            res.close();
            s.close();
            connection.close();

            return new Voucher(voucherId, voucherSig);
        }
        catch (Exception e)
        {
            return null;
        }
    }

    public Voucher(byte[] id, byte[] signature)
    {
        this.id = id;
        this.signature = signature;
    }

    public byte[] getId()
    {
        return id;
    }

    public byte[] getSignature()
    {
        return signature;
    }

    /**
     * Determine is this {@code Voucher} object has been marked as spent, i.e.:
     * if this vouchers id present in the {@code spent_vouchers} table in the
     * database.
     *
     * @return True if the voucher has been marked as spent, false otherwise.
     */
    public boolean isSpent()
    {
        try
        {
            Connection connection = DatabaseHelper.getConnection(
                    DatabaseHelper.DB_URL_VOUCHER);
            Statement s = connection.createStatement();

            ResultSet res = s.executeQuery("select * from "
                    + DatabaseHelper.SPENT_VOUCHERS_TBL
                    + " where " + DatabaseHelper.SPENT_VOUCHERS_COL_ID
                    + " = \"" + Base64.toBase64String(getId()) + "\"");

            // Check if voucher id is present in spent vouchers table
            if (!res.isBeforeFirst())
            {
                return false;
            }

            res.close();
            s.close();
            connection.close();

            /*
             * Voucher id present in spent vouchers table, voucher has already
             * been spent
             */
            return true;
        }
        catch (Exception e)
        {
            // TODO Auto-generated catch block
            e.printStackTrace();
            return true;
        }
    }

    /**
     * Mark this voucher as spent, i.e.: add its id to the
     * {@code spent_vouchers} table in the database.
     *
     * @return True if the operation was successful, false otherwise.
     */
    public boolean markAsSpent()
    {
        String voucherIdB64 = Base64.toBase64String(getId());
        try
        {
            Connection connection = DatabaseHelper.getConnection(
                    DatabaseHelper.DB_URL_VOUCHER);
            Statement s = connection.createStatement();

            // Add to spent vouchers table
            s.executeUpdate("insert into " + DatabaseHelper.SPENT_VOUCHERS_TBL
                    + " value(\"" + voucherIdB64 + "\")");

            s.close();
            connection.close();

            connection = DatabaseHelper.getConnection(
                    DatabaseHelper.DB_URL_USER);
            s = connection.createStatement();

            // Remove from vouchers table
            s.executeUpdate("delete from " + DatabaseHelper.VOUCHERS_TBL
                    + " where " + DatabaseHelper.VOUCHERS_COL_ID + " = \""
                    + voucherIdB64 + "\"");

            s.close();
            connection.close();

            return true;
        }
        catch (Exception e)
        {
            // TODO Auto-generated catch block
            e.printStackTrace();
            return false;
        }
    }

    /**
     * Write the voucher out to the database.
     */
    public void writeToDb()
    {
        /*
         * Voucher would be written to database at this point assigning it
         * to userId
         */
        String voucherIdB64 = Base64.toBase64String(getId());
        String voucherSigB64 = Base64.toBase64String(getSignature());

        try
        {
            Connection connection = DatabaseHelper.getConnection(
                    DatabaseHelper.DB_URL_USER);

            Statement s = connection.createStatement();
            s.executeUpdate("insert into " + DatabaseHelper.VOUCHERS_TBL
                    + " value(\"" + voucherIdB64 + "\",\""
                    + voucherSigB64 + "\")");
            s.close();
            connection.close();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
}
