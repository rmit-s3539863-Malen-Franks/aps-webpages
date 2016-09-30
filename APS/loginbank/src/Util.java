import java.security.NoSuchAlgorithmException;
import java.security.SecureRandom;
import java.security.spec.RSAKeyGenParameterSpec;
import org.bouncycastle.crypto.AsymmetricCipherKeyPair;
import org.bouncycastle.crypto.generators.RSAKeyPairGenerator;
import org.bouncycastle.crypto.params.RSAKeyGenerationParameters;

public class Util
{
    private static final int KEY_SIZE = 512;
    private static final int CERTAINTY = 64;

    /**
     * Generate a sequence of random bytes.
     *
     * @param count
     *            number of bytes to generate
     * @return array of randomly generated bytes
     */
    public static byte[] getRandomBytes(int count)
    {
        byte[] bytes = new byte[count];
        try
        {
            SecureRandom.getInstanceStrong().nextBytes(bytes);
        }
        catch (NoSuchAlgorithmException e)
        {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }
        return bytes;
    }

    /**
     * Generate an RSA key pair.
     *
     * @return RSA key pair
     */
    public static AsymmetricCipherKeyPair generateKeyPair()
    {
        // Generate an RSA key pair.
        RSAKeyPairGenerator generator = new RSAKeyPairGenerator();
        try
        {
            generator.init(new RSAKeyGenerationParameters(
                    RSAKeyGenParameterSpec.F4, SecureRandom.getInstanceStrong(),
                    KEY_SIZE, CERTAINTY));
        }
        catch (NoSuchAlgorithmException e)
        {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }
        return generator.generateKeyPair();
    }
}
