#!/bin/bash
echo ""
echo "Spryker SDK Installer"
echo ""

# Create destination folder
DESTINATION=$1
DESTINATION=${DESTINATION:-/opt/spryker-sdk}


mkdir -p "${DESTINATION}" &> /dev/null

if [ ! -d "${DESTINATION}" ]; then
    echo "Could not create ${DESTINATION}, please use a different directory to install the Spryker SDK into:"
    echo "./installer.sh /your/writeable/directory"
    exit 1
fi

# Find __ARCHIVE__ maker, read archive content and decompress it
ARCHIVE=$(awk '/^__ARCHIVE__/ {print NR + 1; exit 0; }' "${0}")
tail -n+"${ARCHIVE}" "${0}" | tar xpJ -C "${DESTINATION}"

${DESTINATION}/bin/spryker-sdk.sh sdk:init:sdk
${DESTINATION}/bin/spryker-sdk.sh sdk:update:all


if [[ -e ~/.bashrc ]]
then
    echo "alias spryker-sdk=\"${DESTINATION}/bin/spryker-sdk.sh\"" >> ~/.bashrc && source ~/.bashrc
    echo 'Created alias in ~/.bashrc';
elif [[ -e ~/.zshrc ]]
then
    echo "alias spryker-sdk=\"${DESTINATION}/bin/spryker-sdk.sh\"" >> ~/.zshrc  && source ~/.zshrc
    echo 'Created alias in ~/.zshrc';
else
  echo ""
  echo "Installation complete."
  echo "Add alias for your system spryker-sdk=\"${DESTINATION}/bin/spryker-sdk.sh\""
  echo ""
fi

# Exit from the script with success (0)
exit 0

__ARCHIVE__
�7zXZ  �ִF !   t/�����] 1J��7:@C����{�X�b#�"�=�מN�k�^�������,������LI�cS���v��8�W��I��8G�ܩ�2k��-x��;S�>�sFu��k	|�$�q5}�B;����1i��jݵd�&�U7rZ�9#Y1r��F�kǟ���U��1B���M��|���Q�so��L�	��� ?������9�jWަW�KI�ǃ�I��V�����_<�h^���ui�'��oo����
=��@�L �N�����7���k7-��x�S��9[�8���g���Z�~w��Nb��訃��(!�y;��^k�fU�������G��K�1U�w��782����p�\�Կ���P��dOjXʏ_	����g���R:���E7!��Db��4e�hpu�|��� ��lמ�s�7�W��[��Y��%o�AeN�A�Lʎ=�*��� �V��5,,k�/N}�Iͯ���;a�y-�"˂\V���� 7�I��[�M�䡭�5���P
���9�X�%Y�@`r��(f��-K	IN��p
���Ϣ�}��ٚtc~��NFz�{a}�Iu�bvj�w�ɐ�O%�]��G6f2�<����s�!Bc�^���ϭ7Q��h�TN�,����<$��6����j�([�2��I��t���{R�힏�����R���	Yb�r�Tn3�l}8�P���'������Y|��3�X1F'M���/q�nd!�4εD��4ll8�Ɂ��Fk����Pw�h��B��1�'- e���: -�Z�� �=7GQ$I�w�V��$🵹k� �7����00�@u�r��$ԸX��T�K�D���WȳD]K$s���� S��
����?"��t���a���Ն�؉�}X��By����z��Cy���� �����ܜ�ݰ�h
�+i��� p�b���n�pB=~���`��kc����H���k�0����׆h vh%�ן�#�q�K
�B��h�df�QM�����d%�X������B�O�YOz}��>JGm�mq2R�͆7d9�R�Ο��*��"�~	�{���� E'h��­8BQr,��b� P~�vB��A�<��L��j�v��߮������R?(g����F��SI��o����V2b;h/F5���V�1(��w+���WnH��CE1S�3��I��/qS��$�x���R9� �]%?�*�����r��E�*kH�	�BZq��3����关����;.b��*�q���唇��l6"a�{��"2|Į�5�O&*m��~E��zڦqf�Q�/l�&������	�H�ϋ��Ҭ�@o��,GN^D���<4y��$o�Qr�"׍���x7�+u��ʠ85A�9� �m2�>d�\��s�v��fT��sK��5�'��3�z
}���RK���	��>�k:nIe��@����3$�d����kwQ�$�=���3�:����t]M�D}2�^vF(�>��<��)N��xn�x�z}z�<�����]�*T��s)��V�����w8,��8�HM�9S���+�ED�i����u�=ը8�Y&�dy�o ���~+�ҳ���R8�~W�Xq��G���l�W�d��0��Ų�]���S
7���+tx�R(u(V�'�����0Es�dv��6hs�&Cc.�����Y��>&[G#����JaGS�0�0��?噣Ƌx�~�W,��Hm�N�W2�1H�(�9	�X��tE��F��k���:ֽ�e]q�)�_aͳ��'x�M�m�{	phr�cM�#�� ��0��8���J�[���N��g������f&z��UV�x�c�U��P��d���za�[��&����q�j͸2�
�M��5'mڳ�Jj�(�\�Qn7��V�Q�F�KX���s&z���[b�:\m������wB{�F��Q��%V��`Z���6�:ܜ�Mi&��9�	�-�{Olo@>u�����TF��u���+.Lx��\y�k��)r�����.��>�9(�Ƕє��z�=g_R5����bX�ᴗUV���
��q���/�֖f�A�sk��=X�/ǽ=i`��ڰ(7��_hX�vE٣(Q)��2&�����������X��= ��n<\���������hx�bgL%С01d(��$9����`ѻ����|g�uƆD���C)]4Q�J�2^'�C�	
�D�T���0zn��;Uq�wD	�L�dE�28[`�DE����2�U���Sx��|�0�A!���O�竄���^��[71�E�mD�Z��O��%��"�Ttu�}��:4�_䞚鮖zW��>l�~;Y��T=��a�bn-�4p���P�I�	�Q|�l��K��W��g��Vl�f������q�gUt.��]��=���˝��5&�0-{o��E�����h�+Ѷ]��`P=ڿ��9�
u�l$Ah~���� ��ZaD˪׵r�JJS���C��rp�DHUi#ܨ���mҐ�Uq�<P��m�c��_��1_V�3A�Qu�X��++�G��!�H�_<��пC�۝��^�Rţ+�lM�M���X=�Q᭐}���.d@��bΓ%EL���װ}�1��f
R?�}�lf\����1�)7nZ�5騆7E
�ӂ[�%��Z�*��r"}-�R#(ܨ&�7�[,\8��7�mPRq1�����qP�B4в�=	���B��o�RH�2`��^�T�����W��v��K����^��b�0:8O�̀p2�����Bo����yr��G��y�U����i'�˭u	��������H���6�?N*�W����xX�����s^���f��<��O�fh�mXB�/�Jpo���;�Ю+Z��L��Ԏ����0������l��pU�E����~1�>��8��J�?�̰H|uN^ Q��Fm�͝Z�b.n2ލ/���?i4�f�r�!�W�������X�^A�V��3+1M�ԗ�z�
tӺ7�E���A!g:\JO���T��(ܗco���E�g �Fk21y�hI�H�಺҈��lS;pt��j���~7Qc�TRa�[gD�b�^Z�r'�g��Y��	Ţ����R^p��-�5�"�p�q�`PIMڳmޓ,]h-�Q�b��=B����H^�}_wvw����:5`����p%�Μ���(_���Å�J�N�Ia/��=kQؕ�3!�b��݉��Z{���QQ&:� %��#k%���6���EnT�I`,��]4��bE�&l�(�=� �v!�J���6��J;�q'wl��e�eWw=�k��Jx7eI~���W��9A�ku'�f��\��8ǡ��W�A�O��|���Z���'5�����Š�&�N��N�zf���-bU٦y�oL`�h�j���/խ&�9��@��s�T30�h��}&m*4��iM�_�Ş�R�G����;�e#�Z@�C�:JKv֠L�E�8���
$j�{�l,�Ǖ�ҵ�b)
��%*3�1/)P�`�����V1֔W�yR?��L��M� s'�V����C	��w���� ����N�><<C/Ǐ����<��[�ok�V��\���5/<!/����]���ܫ�����D*��2>2�>��[��&ȸ��7w@��P}�mI|�"���YWʸ�Vt3J?1�~�:Oj}�w���(m�h^�F"�I���ܭn��R�P|~Z=hz��%��B���Wr�Y���pP��o��JE�[f�O#x�EY@�����:��"��gK?�����X��f��;x�T�E�;��%L�'R�Z�%K'��i�ca[P�+(�@���������7�����LY�W�3Ӓ<�嬋���c���sZ*��H�My���!A��	i�0:O)��n����E�%b��`ڤ1�\D{j���
z:��Q�8]�~�Q�w%G����O`P*gH�0�Ns���p�U%�s��~<��S쭷Mm�����׽�9�� �E�-̯y�r���%3em-�"�*K�k�):��P��W�Ղ�B�J�
V@&OV�t�bo�ߔ�CCR�@�^�qL��i \i�K��8='�(h��h�êf��
�	�ɶ��˱�ͅ��RvC��̘jStn����L5��@��#������]�.���#7D�������d+���<V��Y9��Dm��^�������fѬ�Nˠ�r���G������������=ʐ��r�u������x��_�٧�ֻHZ�����}�rq9v�rP���A�jS1p��)�a'7�F.���UN�&�.�u�Q��]3���%T0`� �wk2q���"̌I�*iְ�;M��W�6׶^�7�w�9f~��f1&��	n�ſ������T<�[/K	Z��90���"t4u[�J	Y������sy��O�y�E��OZ4 ��!@��
���Zۑf�Dm�9�_)�Zn*���ލ�yɈ|�֭�
[�3?kC6�/!�_RȊӭ�bq�t���2x:}�r�'���dx�BW���KjS�3��[+��D�6��%g�2x��!�ؤv�9F�b^7�`���݇L��sֿ���\5=YK[~Ѩ���|a�(�J]$�mH��âY��k�j��~�#	O���Vo]��M��O�1�a�I�֑1�����|.���5��0F��0�ZN$~_$N���ݛ��v�i���m�	�@+�fz)��R�Yp�Ir���h�c�rQ&S���i��� tqyA�Z&S�ޢ��]NK>+.��Cĉ���8���:���D������{"�m#bvsBֳ�d";�Ȁ�|F�+`���D;�v^��=�\	����^�퇦���	Z���s���,��Z01���:��!8)^g�<u�k��"4����zIq����ʑY�����r�^��%�i�w�~*4�[=�m`�o^���tfF�w0�6ߔ;^�����IL�݃鎃BP3=��# z�N�ذ���vN��V*�pޑ"�8��q���	�<��>·sJ�q���D7��3+C�ݣiԼ�C��jsG��]\Gq����;zN]-\�hʨ�����	G���}i�#A=
T$4�)
���V�x� 5��z�f��G@��|N�g�	�T�`��Ӽ����3�x��c�k�����+����F��u�]��@�S��`&�[h8m0��X"aP�Kʳ�B  ?;$�D�ï �)�� E2E���g�    YZ