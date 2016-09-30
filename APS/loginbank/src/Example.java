
import java.sql.Connection;
import java.sql.Statement;
import java.util.Scanner;
import org.bouncycastle.crypto.CryptoException;
import org.bouncycastle.util.encoders.Base64;

public class Example
{
    private static final int TEST_ACC_NO = 123456;

    public static void main(String[] args) throws CryptoException
    {
        Bank bank = Bank.readFromDb();
        if (bank == null)
        {
            System.err.print("Unable to read bank keys from db, "
                    + "generating new ones...");
            bank = new Bank(Util.generateKeyPair());
            System.err.println(" Done!");

            System.err.print("Writing new bank keys to db...");
            if (!bank.writeToDb())
            {
                System.err.println("Fatal error: Unable to write new keys to "
                        + "db. Exitting...");
                System.exit(1);
            }
            System.err.println(" Done!");
        }

        int accNo = TEST_ACC_NO;

        System.out.println(bank);
        System.out.println();

        Scanner sc = new Scanner(System.in);
        System.out.print("Number of vouchers to produce: ");
        int numVouchers = sc.nextInt();
        sc.close();

        System.out.println("----------------------------------------");

        generateVouchers(bank, accNo, numVouchers);
    }

    /**
     * Generate a given number of vouchers.
     *
     * @param bank
     *            Banking authority to generate vouchers
     * @param accNo
     *            Bank account number of customer requesting vouchers
     * @param num
     *            Number of vouchers requested
     * @throws CryptoException
     *
     * @return True if vouchers were generated, otherwise false.
     */
    private static boolean generateVouchers(Bank bank, int accNo,
            int num) throws CryptoException
    {
        /*
         * Checks customer has sufficient funds for requested vouchers and
         * subtracts funds if so, otherwise returns false
         */
        if (!bank.withdraw(accNo, num))
        {
            System.err.println("Insufficient funds for vouchers reuqested");
            return false;
        }

        long startTotal = System.nanoTime();

        // Generate vouchers
        for (int i = 0; i < num; i++)
        {
            long start = System.nanoTime();

            System.out.println();
            System.out.println("VOUCHER " + (i + 1));
            System.out.println();

            generateVoucher(bank);

            long end = System.nanoTime();
            System.out.printf("Generated voucher in %.2f ms\n",
                    (end - start) / 1e6);
            System.out.println("----------------------------------------");
        }

        long endTotal = System.nanoTime();
        System.out.printf("Generated %d vouchers in %.2f ms\n", num,
                (endTotal - startTotal) / 1e6);

        return true;
    }

    /**
     * Generate a single voucher.
     *
     * @param bank
     *            Banking authority to generate voucher
     * @throws CryptoException
     */
    private static void generateVoucher(Bank bank)
            throws CryptoException
    {
        VoucherBlinder vb = new VoucherBlinder(bank.getPublic());
        byte[] blindedVoucherRequest = vb.generateBlindedVoucherRequest();
        printVoucherRequest(blindedVoucherRequest);

        byte[] signedVoucher = bank.sign(blindedVoucherRequest);
        printBankSignature(signedVoucher);

        Voucher voucher = vb.createVoucher(signedVoucher);
        printVoucher(voucher);

        writeVoucherToDb(voucher);

        System.out.println(bank.verify(voucher)
                ? "Voucher verified."
                : "Error verifying voucher!");
    }

    private static void writeVoucherToDb(Voucher voucher)
    {
        /*
         * Voucher would be written to database at this point assigning it
         * to userId
         */
        String voucherIdB64 = Base64.toBase64String(voucher.getId());
        String voucherSigB64 = Base64.toBase64String(voucher.getSignature());

        try
        {
            Connection connection = DatabaseHelper.getConnection(
                    DatabaseHelper.DB_URL_VOUCHER);

            Statement s = connection.createStatement();
            s.executeUpdate("insert into vouchers value(\"" + voucherIdB64
                    + "\",\"" + voucherSigB64 + "\")");

            s.close();
            connection.close();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    private static void printVoucherRequest(byte[] blindedVoucherRequest)
    {
        System.out.println("Blinded voucher ID to be signed by bank:");
        System.out.println(Base64.toBase64String(blindedVoucherRequest));
        System.out.println();
    }

    private static void printBankSignature(byte[] signature)
    {
        System.out.println("Bank's (blinded) signature:");
        System.out.println(Base64.toBase64String(signature));
        System.out.println();
    }

    private static void printVoucher(Voucher voucher)
    {
        System.out.println("Voucher:");
        System.out.println("       ID: " + Base64.toBase64String(voucher
                .getId()));
        System.out.println("Signature: " + Base64.toBase64String(voucher
                .getSignature()));
        System.out.println();
    }
}
