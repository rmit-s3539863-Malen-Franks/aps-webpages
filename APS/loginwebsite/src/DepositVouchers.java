import java.util.ArrayList;
import java.util.List;
import org.bouncycastle.util.encoders.Base64;

/**
 * <p>
 * Class to be called to deposit vouchers into a given account.
 * </p>
 * <p>
 * Called using:
 *
 * <pre>
 * java DepositVouchers {@literal <}accNo{@literal >} {@literal <}voucherId{@literal >}...
 * </pre>
 * </p>
 * <p>
 * Prints {@code true} to {@code stdout} if vouchers were successfully
 * deposited, {@code false} otherwise.
 * </p>
 */
public class DepositVouchers
{
    private static final int ARGNO_ACC_NO = 0;
    private static final int ARGNO_FIRST_VOUCHER_ID = 1;

    /**
     * @param args
     *            Bank account number to deposit vouchers into, followed by one
     *            or more Voucher IDs
     */
    public static void main(String[] args)
    {
        if (args.length < 2)
        {
            System.err.println("Invalid parameters supplied, need: accNo, "
                    + "voucherId...");
            System.exit(1);
        }

        int accNo = Integer.parseInt(args[ARGNO_ACC_NO]);

        Bank bank = Bank.readFromDb();
        if (bank == null)
        {
            System.err.println("Fatal error: Unable to read bank keys from db. "
                    + "Exitting...");
            System.out.println(false);
            System.exit(1);
        }

        List<Voucher> vouchers = new ArrayList<>();

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
                System.err.println("Error: Voucher ID not valid Base64.");
                System.out.println(false);
                System.exit(1);
            }

            /*
             * Check that the voucher exists AND has a valid signature according
             * to
             * the issuing authority AND has not already been spent
             */
            if (voucher == null || !bank.verify(voucher) || voucher.isSpent())
            {
                System.err.println("Unable to deposit vouchers. At least one "
                        + "voucher is either invalid, could not be verified by "
                        + "the issuing authority or has already been spent.");
                System.out.println(false);
                System.exit(1);
            }

            vouchers.add(voucher);
        }

        // Deposit all vouchers into the given account
        bank.deposit(accNo, vouchers.size());
        // Mark all vouchers as spent
        for (Voucher voucher : vouchers)
        {
            voucher.markAsSpent();
        }

        // Indicate successful deposit
        System.out.println(true);
    }
}
