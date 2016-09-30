import org.bouncycastle.util.encoders.Base64;

/**
 * <p>
 * Class to be called to verify vouchers.
 * </p>
 * <p>
 * Called using:
 *
 * <pre>
 * java VerifyVouchers {@literal <}voucherId{@literal >}...
 * </pre>
 * </p>
 * <p>
 * Prints {@code true} to {@code stdout} if vouchers were all verified,
 * {@code false} otherwise.
 * </p>
 */
public class VerifyVouchers
{
    private static final int ARGNO_FIRST_VOUCHER_ID = 0;

    /**
     * @param args
     *            One or more voucher IDs
     */
    public static void main(String[] args)
    {
        if (args.length < 1)
        {
            System.err.println("Invalid parameters supplied, need: "
                    + "voucherId...");
            System.exit(1);
        }

        Bank bank = Bank.readFromDb();
        if (bank == null)
        {
            System.err.println("Fatal error: Unable to read bank keys from db. "
                    + "Exitting...");
            System.out.println(false);
            System.exit(1);
        }

        for (int i = ARGNO_FIRST_VOUCHER_ID; i < args.length; i++)
        {
            String voucherIdB64 = args[i];

            Voucher voucher = null;
            try
            {
                voucher = Voucher.readFromDb(Base64.decode(voucherIdB64));
            }
            catch (Exception e)
            {
                System.err.println("Error parsing voucher ID.");
                System.out.println(false);
                System.exit(1);
            }

            /*
             * Check that the voucher exists AND has a valid signature according
             * to the issuing authority AND has not already been spent
             */
            if (voucher == null || !bank.verify(voucher) || voucher.isSpent())
            {
                System.err.println("Unable to verify vouchers. At least one "
                        + "voucher is either invalid, could not be verified by "
                        + "the issuing authority or has already been spent.");
                System.out.println(false);
                System.exit(1);
            }
        }

        // If this point has been reached, all vouchers were verified
        System.out.println(true);
    }
}
