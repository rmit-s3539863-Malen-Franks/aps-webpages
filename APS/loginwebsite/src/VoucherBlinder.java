import java.math.BigInteger;
import org.bouncycastle.crypto.CryptoException;
import org.bouncycastle.crypto.digests.SHA1Digest;
import org.bouncycastle.crypto.engines.RSABlindingEngine;
import org.bouncycastle.crypto.generators.RSABlindingFactorGenerator;
import org.bouncycastle.crypto.params.RSABlindingParameters;
import org.bouncycastle.crypto.params.RSAKeyParameters;
import org.bouncycastle.crypto.signers.PSSSigner;

public class VoucherBlinder
{
    private byte[] voucherID;
    private RSABlindingParameters blindingParams;

    public VoucherBlinder(RSAKeyParameters pubKey)
    {
        // Create a 128-bit globally unique ID for the coin.
        voucherID = Util.getRandomBytes(16);

        // Generate a blinding factor using the bank's public key
        RSABlindingFactorGenerator blindingFactorGenerator =
                new RSABlindingFactorGenerator();
        blindingFactorGenerator.init(pubKey);

        BigInteger blindingFactor = blindingFactorGenerator
                .generateBlindingFactor();

        blindingParams = new RSABlindingParameters(pubKey, blindingFactor);
    }

    /**
     * Blind the coin ID and generate a blinded voucher to be signed by the
     * bank.
     *
     * @return Byte array of blinded voucher
     * @throws CryptoException
     *             See
     *             {@link org.bouncycastle.crypto.signers.PSSSigner#generateSignature()
     *             PSSSigner.generateSignature()}
     */
    public byte[] generateBlindedVoucherRequest() throws CryptoException
    {
        PSSSigner signer = new PSSSigner(new RSABlindingEngine(),
                new SHA1Digest(), 20);
        signer.init(true, blindingParams);

        signer.update(voucherID, 0, voucherID.length);

        byte[] blindedVoucher = signer.generateSignature();

        return blindedVoucher;
    }

    /**
     * Unblind the bank's signature and create a new voucher using the ID and
     * the unblinded signature.
     *
     * @return The resulting voucher
     */
    public Voucher createVoucher(byte[] signedBlindedVoucher)
    {
        RSABlindingEngine blindingEngine = new RSABlindingEngine();
        blindingEngine.init(false, blindingParams);

        byte[] sig = blindingEngine.processBlock(signedBlindedVoucher, 0,
                signedBlindedVoucher.length);

        return new Voucher(voucherID, sig);
    }
}
