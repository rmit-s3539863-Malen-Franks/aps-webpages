import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.TimeUnit;
import org.bouncycastle.crypto.CryptoException;
import org.bouncycastle.util.encoders.Base64;

/**
 * <p>
 * Class to be called to generate vouchers from a given account.
 * </p>
 * <p>
 * Called using:
 *
 * <pre>
 * java GenerateVouchers {@literal <}accNo{@literal >} {@literal <}numVouchers{@literal >}
 * </pre>
 * </p>
 * <p>
 * Prints a log of voucher generation to {@code stdout}.
 * </p>
 */
public class GenerateVouchers
{
    private static final int ARGNO_ACC_NO = 0;
    private static final int ARGNO_NUM_VOUCHERS = 1;

    /**
     * @param args
     *            Bank account number to withdraw the funds from, followed by
     *            the number of vouchers required
     */
    public static void main(String[] args)
    {
        if (args.length != 2)
        {
            System.err.println("Invalid parameters supplied, need: accNo, "
                    + "numVouchers");
            System.exit(1);
        }

        int accNo = Integer.parseInt(args[ARGNO_ACC_NO]);
        int numVouchers = Integer.parseInt(args[ARGNO_NUM_VOUCHERS]);

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
                System.err.println();
                System.err.println("FATAL ERROR: Unable to write new keys to "
                        + "db. Exitting...");
                System.exit(1);
            }
            System.err.println(" Done!");
        }

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
     *
     * @return True if vouchers were generated, otherwise false.
     */
    private static boolean generateVouchers(Bank bank, int accNo, int num)
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

        long start = System.nanoTime();

        // Generate vouchers
        for (int i = 0; i < num; i++)
        {
            System.out.println("---- BEGIN voucher " + (i + 1)
                    + " generation ----");
            try
            {
                generateVoucher(bank);
            }
            catch (CryptoException e)
            {
                System.err.println("Error occured while generating voucher!");
                e.printStackTrace();
                return false;
            }
            System.out.println("---- END voucher " + (i + 1)
                    + " generation ----");
            System.out.println();
        }

        long end = System.nanoTime();
        System.out.printf("Generated %d vouchers in %.2f ms\n", num,
                (end - start) / 1e6);

        return true;
    }

    /**
     * <p>
     * [UNUSED]
     * </p>
     * <p>
     * Makes use of multiple threads to generate vouchers quicker.
     * </p>
     * <p>
     * Generate a given number of vouchers.
     * </p>
     *
     * @param bank
     *            Banking authority to generate vouchers
     * @param accNo
     *            Bank account number of customer requesting vouchers
     * @param num
     *            Number of vouchers requested
     *
     * @return True if vouchers were generated, otherwise false.
     *
     * @throws CryptoException
     * @throws InterruptedException
     */
    @SuppressWarnings("unused")
    private static boolean generateVouchersThreaded(Bank bank, int accNo,
            int num)
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

        // Create thread pool for generating vouchers
        ExecutorService service = Executors.newFixedThreadPool(
                Runtime.getRuntime().availableProcessors());

        long start = System.nanoTime();

        // Generate vouchers
        for (int i = 0; i < num; i++)
        {
            service.submit(new Runnable()
            {
                @Override
                public void run()
                {
                    try
                    {
                        generateVoucher(bank);
                    }
                    catch (CryptoException e)
                    {
                        e.printStackTrace();
                    }
                }
            });
        }

        // Finished with thread pool, shut down when done
        service.shutdown();
        try
        {
            service.awaitTermination(1, TimeUnit.HOURS);
        }
        catch (InterruptedException e)
        {
            Thread.currentThread().interrupt();
        }

        long end = System.nanoTime();
        System.out.printf("Generated %d vouchers in %.2f ms\n", num,
                (end - start) / 1e6);

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
        VoucherBlinder blinder = new VoucherBlinder(bank.getPublic());
        byte[] blindedVoucherRequest = blinder.generateBlindedVoucherRequest();

        printVoucherRequest(blindedVoucherRequest);
        System.out.println();

        byte[] signedVoucher = bank.sign(blindedVoucherRequest);

        printBankSignature(signedVoucher);
        System.out.println();

        Voucher voucher = blinder.createVoucher(signedVoucher);

        printVoucher(voucher);

        voucher.writeToDb();
    }

    private static void printVoucherRequest(byte[] blindedVoucherRequest)
    {
        System.out.println("Blinded voucher ID to be signed by bank:");
        System.out.println(Base64.toBase64String(blindedVoucherRequest));
    }

    private static void printBankSignature(byte[] signature)
    {
        System.out.println("Bank's (blinded) signature:");
        System.out.println(Base64.toBase64String(signature));
    }

    private static void printVoucher(Voucher voucher)
    {
        System.out.println("Voucher:");
        System.out.println("       ID: " + Base64.toBase64String(voucher
                .getId()));
        System.out.println("Signature: " + Base64.toBase64String(voucher
                .getSignature()));
    }
}
