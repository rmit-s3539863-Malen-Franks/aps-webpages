import java.math.BigInteger;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import org.bouncycastle.crypto.AsymmetricCipherKeyPair;
import org.bouncycastle.crypto.digests.SHA1Digest;
import org.bouncycastle.crypto.engines.RSAEngine;
import org.bouncycastle.crypto.params.RSAKeyParameters;
import org.bouncycastle.crypto.signers.PSSSigner;
import org.bouncycastle.util.encoders.Base64;

public class Bank
{
    private AsymmetricCipherKeyPair keyPair;

    /**
     * Create a {@code Bank} from the keys stored in a database for a given bank
     * id.
     *
     * @return Bank object corresponding to the keys stored in the database.
     */
    public static Bank readFromDb()
    {
        try
        {
            Connection connection = DatabaseHelper.getConnection(
                    DatabaseHelper.DB_URL_BANK);
            Statement s = connection.createStatement();

            ResultSet res = s.executeQuery("select * from "
                    + DatabaseHelper.BANK_KEYS_TBL);

            // Move to selected row
            res.next();

            String pubExponentB64 = res.getString(
                    DatabaseHelper.BANK_KEYS_COL_PUB_EXP);
            BigInteger pubExponent = new BigInteger(
                    Base64.decode(pubExponentB64));

            String pubModulusB64 = res.getString(
                    DatabaseHelper.BANK_KEYS_COL_PUB_MOD);
            BigInteger pubModulus = new BigInteger(
                    Base64.decode(pubModulusB64));

            String privExponentB64 = res.getString(
                    DatabaseHelper.BANK_KEYS_COL_PRIV_EXP);
            BigInteger privExponent = new BigInteger(
                    Base64.decode(privExponentB64));

            String privModulusB64 = res.getString(
                    DatabaseHelper.BANK_KEYS_COL_PRIV_MOD);
            BigInteger privModulus = new BigInteger(
                    Base64.decode(privModulusB64));

            res.close();
            s.close();
            connection.close();

            return new Bank(new AsymmetricCipherKeyPair(
                    new RSAKeyParameters(false, pubModulus, pubExponent),
                    new RSAKeyParameters(true, privModulus, privExponent)));
        }
        catch (Exception e)
        {
            return null;
        }
    }

    /**
     * @param keys
     *            RSA key pair this bank uses to sign vouchers
     */
    public Bank(AsymmetricCipherKeyPair keys)
    {
        this.keyPair = keys;
    }

    /**
     * Write the bank keys out to the database.
     *
     * @return True if the database operation was successful, false otherwise.
     */
    public boolean writeToDb()
    {
        BigInteger pubExponent = getPublic().getExponent();
        String pubExponentB64 = Base64.toBase64String(
                pubExponent.toByteArray());

        BigInteger pubModulus = getPublic().getModulus();
        String pubModulusB64 = Base64.toBase64String(
                pubModulus.toByteArray());

        BigInteger privExponent = getPrivate().getExponent();
        String privExponentB64 = Base64.toBase64String(
                privExponent.toByteArray());

        BigInteger privModulus = getPrivate().getModulus();
        String privModulusB64 = Base64.toBase64String(
                privModulus.toByteArray());

        try
        {
            Connection connection = DatabaseHelper.getConnection(
                    DatabaseHelper.DB_URL_BANK);
            Statement s = connection.createStatement();

            s.executeUpdate("insert into " + DatabaseHelper.BANK_KEYS_TBL
                    + " value(\"" + pubExponentB64 + "\",\"" + pubModulusB64
                    + "\",\"" + privExponentB64 + "\",\"" + privModulusB64
                    + "\")");

            s.close();
            connection.close();

            return true;
        }
        catch (SQLException e)
        {
            return false;
        }
    }

    /**
     * Sign a blinded voucher request.
     *
     * @param blindedVoucherRequest
     * @return The resulting blinded signature.
     */
    public byte[] sign(byte[] blindedVoucherRequest)
    {
        RSAEngine engine = new RSAEngine();
        engine.init(true, keyPair.getPrivate());

        return engine.processBlock(
                blindedVoucherRequest, 0, blindedVoucherRequest.length);
    }

    /**
     * Verify that the voucher has a valid signature.
     *
     * @param voucher
     *            Voucher to be verified
     * @return True if the signature if valid, false otherwise.
     */
    public boolean verify(Voucher voucher)
    {
        // Verify that the coin has a valid signature using our public key.
        byte[] id = voucher.getId();
        byte[] signature = voucher.getSignature();

        PSSSigner signer = new PSSSigner(new RSAEngine(), new SHA1Digest(), 20);
        signer.init(false, keyPair.getPublic());

        signer.update(id, 0, id.length);

        return signer.verifySignature(signature);
    }

    /**
     * Deposit the given amount into the given accNo.
     *
     * @param accNo
     *            Account number to deposit amount into
     * @param amount
     *            Amount to be deposited
     * @return True if the deposit was successful, false otherwise.
     */
    public boolean deposit(int accNo, int amount)
    {
        try
        {
            Connection connection = DatabaseHelper.getConnection(
                    DatabaseHelper.DB_URL_BANK);
            Statement s = connection.createStatement();

            ResultSet res = s.executeQuery("select * from "
                    + DatabaseHelper.BANK_CUSTOMERS_TBL
                    + " where acc_no = " + accNo);

            res.next();

            int balance = res.getInt(DatabaseHelper.BANK_CUSTOMERS_COL_BAL);
            balance += amount;

            s.executeUpdate("update " + DatabaseHelper.BANK_CUSTOMERS_TBL
                    + " set balance = " + balance
                    + " where acc_no = " + accNo);

            res.close();
            s.close();
            connection.close();

            return true;
        }
        catch (SQLException e)
        {
            // Something went wrong with the database queries
            e.printStackTrace();
            return false;
        }
    }

    /**
     * Withdraw the given amount from the given accNo.
     *
     * @param accNo
     *            Account number to withdraw amount from
     * @param amount
     *            Amount to be withdrawn
     * @return True if sufficient funds in the account to make the
     *         withdrawal and the withdrawal was successful, false otherwise.
     */
    public boolean withdraw(int accNo, int amount)
    {
        if (hasEnoughFunds(accNo, amount) == true)
        {
            try
            {
                Connection connection = DatabaseHelper.getConnection(
                        DatabaseHelper.DB_URL_BANK);
                Statement s = connection.createStatement();

                ResultSet res = s.executeQuery("select * from "
                        + DatabaseHelper.BANK_CUSTOMERS_TBL
                        + " where acc_no = " + accNo);

                res.next();
                int balance = res.getInt(
                        DatabaseHelper.BANK_CUSTOMERS_COL_BAL);
                res.close();

                balance -= amount;

                s.executeUpdate("update " + DatabaseHelper.BANK_CUSTOMERS_TBL
                        + " set balance = " + balance
                        + " where acc_no = " + accNo);

                res.close();
                s.close();
                connection.close();
            }
            catch (SQLException e)
            {
                // Something went wrong with the database queries
                e.printStackTrace();
                return false;
            }
        }
        else
        {
            return false;
        }
        return true;
    }

    /**
     * Check that the account corresponding to {@code accNo} has at least
     * {@code amount} funds.
     *
     * @param accNo
     *            Account number of the account to check
     * @param amount
     *            Amount of funds to check for
     * @return True if the account has at least {@code amount} funds, false
     *         otherwise.
     */
    public boolean hasEnoughFunds(int accNo, int amount)
    {
        int balance = balanceEnquiry(accNo);
        if (balance < amount)
        {
            return false;
        }
        return true;
    }

    /**
     * Query the balance of the account given by {@code accNo}.
     *
     * @param accNo
     *            Account number of the account to check
     * @return The balance of the account, {@code -1} if an error occurred.
     */
    public int balanceEnquiry(int accNo)
    {
        // Return customer balance from database
        int balance = 0;

        try
        {
            Connection connection = DatabaseHelper.getConnection(
                    DatabaseHelper.DB_URL_BANK);
            Statement s = connection.createStatement();

            ResultSet res = s.executeQuery("select * from "
                    + DatabaseHelper.BANK_CUSTOMERS_TBL
                    + " where acc_no = " + accNo);

            res.next();

            balance = res.getInt(DatabaseHelper.BANK_CUSTOMERS_COL_BAL);

            res.close();
            s.close();
            connection.close();
        }
        catch (SQLException e)
        {
            // Something went wrong with the database queries
            e.printStackTrace();
            return -1;
        }

        return balance;
    }

    public RSAKeyParameters getPublic()
    {
        return (RSAKeyParameters) keyPair.getPublic();
    }

    public RSAKeyParameters getPrivate()
    {
        return (RSAKeyParameters) keyPair.getPrivate();
    }

    public String toString()
    {
        return "Bank's RSA KeyPair:\n"
                + "  Public key:\n"
                + "    Modulus:  " + getPublic().getModulus() + "\n"
                + "              " + Base64.toBase64String(
                        getPublic().getModulus().toByteArray()) + "\n"
                + "    Exponent: " + getPublic().getExponent() + "\n"
                + "              " + Base64.toBase64String(
                        getPublic().getExponent().toByteArray()) + "\n"
                + "  Private key:\n"
                + "    Modulus:  " + getPrivate().getModulus() + "\n"
                + "              " + Base64.toBase64String(
                        getPrivate().getModulus().toByteArray()) + "\n"
                + "    Exponent: " + getPrivate().getExponent() + "\n"
                + "              " + Base64.toBase64String(
                        getPrivate().getExponent().toByteArray());
    }
}
