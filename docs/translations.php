<?php

// Automated export

return array (
  'languages' => 
  array (
    0 => 
    array (
      'id' => '1',
      'name' => 'English',
      'locale_POSIX' => 'en_US.UTF-8',
    ),
    1 => 
    array (
      'id' => '2',
      'name' => 'Română',
      'locale_POSIX' => 'ro_RO.UTF-8',
    ),
  ),
  'messages' => 
  array (
    'genericErrorMissingObject' => 
    array (
      'comment' => 'A generic error message shown when the user is trying to access an object that wasn\'t found in the database. Takes two parameters, the class name and the object ID.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'This object doesn\'t exist ({0} #{1}).',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Acest obiect nu există ({0} #1).',
        ),
      ),
    ),
    'genericErrorRights' => 
    array (
      'comment' => 'A very generic error message shown when users try to access a page or perform an action they don\'t have enough permissions for.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'You do not have the necessary permissions to perform this operation.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Nu aveţi permisiunile necesare pentru a efectua această operaţiune.',
        ),
      ),
    ),
    'genericErrorSessionKey' => 
    array (
      'comment' => 'Warning and explanation regarding session keys. Shown only when there is a mismatch.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Session key mismatch! Either you\'re trying to continue an older session, or, if you came here from an external source (website, e-mail), someone is trying to take advantage of your rights.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Eroare a cheiei de sesiune! Fie aţi încercat să continuaţi o sesiune mai veche sau, dacă veniţi aici de la o sursă externă (website, e-mail), cineva încearcă să se folosească în mod abuziv de drepturile dumneavoastră.',
        ),
      ),
    ),
    'genericNoName' => 
    array (
      'comment' => 'Fără nume',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => '(no name)',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => '(nume indisponibil)',
        ),
      ),
    ),
    'lpcAuthCancelResetButton' => 
    array (
      'comment' => 'The label on the button used for canceling the password reset request.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Reset the password recovery request',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Anulează cererea de recuperare a parolei',
        ),
      ),
    ),
    'lpcAuthConfirmRecoverEmail' => 
    array (
      'comment' => 'The confirmation message shown when the password recovery e-mail is sent (or not sent, if the e-mail wasn\'t found in the database). Takes one parameter, the e-mail address supplied by the user.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'If there is any registered user with the e-mail address you have indicated (<tt>{0}</tt>) then the message was sent successfully.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Dacă există vreun utilizator înregistrat care foloseşte adresa e-mail pe care aţi indicat-o (<tt>{0}</tt>) atunci mesajul a fost trimis cu succes.',
        ),
      ),
    ),
    'lpcAuthCreatePasswordFormInfo' => 
    array (
      'comment' => 'The introductory message shown on the password creation form.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Congratulations, you almost finished the registration process! Please enter the password you want to use in the form below.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Felicitări, aproape aţi terminat procesul de înregistrare! Vă rugăm introduceţi parola pe care doriţi să o folosiţi în formularul de dedesubt.',
        ),
      ),
    ),
    'lpcAuthCreatePasswordLabel' => 
    array (
      'comment' => 'The label of the button at the end of the password creation form.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Create account',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Creare cont',
        ),
      ),
    ),
    'lpcAuthCreatePasswordTitle' => 
    array (
      'comment' => 'The title of the password creation page.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Account creation',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Creare cont',
        ),
      ),
    ),
    'lpcAuthDoneCancel' => 
    array (
      'comment' => 'The confirmation message shown when the password reset request is successfully cancelled.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'The password recovery request was successfully reset.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Cererea de recuperare a parolei a fost anulată cu succes.',
        ),
      ),
    ),
    'lpcAuthDoneResetConfirm' => 
    array (
      'comment' => 'The confirmation message shown when the password reset succeeds. Takes one parameter, the username for the user who changed their password. Be advised the username may actually be their e-mail address; also, they have already been logged in automatically at this point.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'The password was changed successfully. You have already been authenticated as {0}, and in the future you can authenticate with the password you just entered.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Parola a fost schimbată cu succes. Aţi fost deja autentificat ca {0}, iar în viitor vă puteţi autentifica cu parola pe care tocmai aţi introdus-o.',
        ),
      ),
    ),
    'lpcAuthEmail' => 
    array (
      'comment' => 'The label for the authentication field "e-mail"',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'E-mail address',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Adresa e-mail',
        ),
      ),
    ),
    'lpcAuthErrAlreadyLoggedOn' => 
    array (
      'comment' => 'The error shown to authenticated users who want to recover a password.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'You are already authenticated!',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Deja sunteţi autentificat!',
        ),
      ),
    ),
    'lpcAuthErrConfirm' => 
    array (
      'comment' => 'The authentication-related error message shown when the password confirmation differs from the password field',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Please make sure you type exactly the same password twice!',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Vă rugăm asiguraţi-vă că scrieţi exact aceeaşi parolă de două ori!',
        ),
      ),
    ),
    'lpcAuthErrFailEmail' => 
    array (
      'comment' => 'The error message shown when the password recovery e-mail couldn\'t be sent. Takes one parameter, the e-mail address supplied by the user.

Be advised this is always related to an existing account, and properly identified by the code based on the e-mail address supplied by the user.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'The password recovery e-mail could not be sent. Either the e-mail address is invalid (<tt>{0}</tt>), or there is a temporary connectivity problem. If the e-mail address is valid, please try again later.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Mesajul e-mail de recuperare a parolei nu a putut fi trimis. Fie această adresă e-mail este invalidă (<tt>{0}</tt>), fie au apărut probleme temporare de conectivitate. Dacă parola este validă vă rugăm încercaţi din nou mai târziu.',
        ),
      ),
    ),
    'lpcAuthErrMinLength' => 
    array (
      'comment' => 'The authentication-related error message shown when the password is too short. Takes one parameter, the minimum valid length.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'The password is too short! Please make sure the password you choose is at least {0} characters long.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Parola este prea scurtă! Vă rugăm asiguraţi-vă că parola pe care o alegeţi are cel puţin {0} caractere.',
        ),
      ),
    ),
    'lpcAuthErrNeedAlpha' => 
    array (
      'comment' => 'The authentication-related error message shown when the password doesn\'t contain any letters.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Please make sure the password you choose contains at least one letter!',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Vă rugăm asiguraţi-vă că parola pe care o alegeţi conţine cel puţin o literă!',
        ),
      ),
    ),
    'lpcAuthErrNeedNumber' => 
    array (
      'comment' => 'The authentication-related error message shown when the password doesn\'t contain any numbers.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Please make sure the password you choose contains at least one number!',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Vă rugăm asiguraţi-vă că parola pe care o alegeţi conţine cel puţin o cifră!',
        ),
      ),
    ),
    'lpcAuthInvalidToken' => 
    array (
      'comment' => 'The error message shown when a user tries to recover a password with an invalid token/e-mail combination.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'The data you\'re trying to use is invalid. Either they have already been used for access, or the token has expired, or you haven\'t copied the complete URL from the e-mail message.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Datele pe care încercaţi să le folosiţi sunt invalide. Fie au fost deja folosite pentru acces, sau cheia de acces a expirat, sau nu aţi copiat adresa în întregime din mesajul e-mail.',
        ),
      ),
    ),
    'lpcAuthInviteBody' => 
    array (
      'comment' => 'The invitation e-mail\'s body. Takes several arguments:
{0} -- the user\'s actual name
{1} -- the value of constant LPC_project_name
{2} -- the value of constant LPC_project_full_name
{3} -- the token URL
{4} -- the e-mail address this e-mail was sent to

It is recommended that you do not overwrite this translation just in order to customize it -- instead, you should redefine your LPC_User\'s descendant variable token_invite_body to point to another translation key, specific to your project.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Dear {0},

Welcome to {2}! In order to finalize the registration process please click on the link below, or copy it to your web browser\'s address bar:

{3}

Thank you,
The {1} team',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Doamna/domnule {0},

Bun venit la {2}! Pentru a finaliza procesul de inregistrare, va rugam apasati pe legatura de dedesubt, sau copiati-o in bara de adrese a navigatorului dumneavoastra web:

{3}

Va multumim,
Echipa {1}',
        ),
      ),
    ),
    'lpcAuthInviteSubject' => 
    array (
      'comment' => 'The invitation e-mail\'s subject. Takes one parameter: the name of the project (the value of constant LPC_full_project_name).

It is recommended that you do not overwrite this translation just in order to customize it -- instead, you should redefine your LPC_User\'s descendant variable token_invite_subject to point to another translation key, specific to your project.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'New {0} account',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Cont nou {0}',
        ),
      ),
    ),
    'lpcAuthLogIn' => 
    array (
      'comment' => 'The label for the authentication "Log in" button',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Log in',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Autentificare',
        ),
      ),
    ),
    'lpcAuthPassword' => 
    array (
      'comment' => 'The label for the authentication field "Password"',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Password',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Parola',
        ),
      ),
    ),
    'lpcAuthPasswordConfirm' => 
    array (
      'comment' => 'The label for the password confirmation field in the password reset form.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Confirm the password',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Confirmaţi parola',
        ),
      ),
    ),
    'lpcAuthPasswordConfirmExplain' => 
    array (
      'comment' => 'The explanations for the password confirmation field shown in the password reset form.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Enter the same password again, for confirmation',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Introduceţi din nou aceeaşi parolă pentru confirmare',
        ),
      ),
    ),
    'lpcAuthPasswordFieldExplain' => 
    array (
      'comment' => 'The explanation for the first password field in the password reset form.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Enter the new password you want to use',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Introduceţi noua parolă pe care doriţi să o utilizaţi',
        ),
      ),
    ),
    'lpcAuthRecover' => 
    array (
      'comment' => 'The label for the authentication link "recover password"',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'I lost my password',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Mi-am pierdut parola',
        ),
      ),
    ),
    'lpcAuthRecoverBody' => 
    array (
      'comment' => 'The body of the recover password e-mail. Takes several arguments:
{0} -- the user\'s actual name
{1} -- the value of constant LPC_project_name
{2} -- the value of constant LPC_project_full_name
{3} -- the token URL
{4} -- the e-mail address this e-mail was sent to

It is recommended that you do not overwrite this translation just in order to customize it -- instead, you should redefine your LPC_User\'s descendant variable token_recover_body to point to another translation key, specific to your project.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Dear {0},

Someone requested the password recovery for the account associated with your e-mail address ({4}). Please click on the link below regardless of whether you have requested it or not (you will have the opportunity to indicate that at the link):

{3}

Thank you,
The {1} team',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Doamnă/domnule {0},

Cineva a solicitat recuperarea parolei pentru contul asociat adresei dumneavoastră e-mail ({4}). Indiferent dacă aţi solicitat recuperarea parolei sau nu, vă rugăm apăsaţi pe legătura de mai jos (dacă nu aţi solicitat recuperarea parolei veţi avea ocazia să indicaţi acest lucru).

{3}

Vă mulţumim,
Echipa {1}',
        ),
      ),
    ),
    'lpcAuthRecoverButton' => 
    array (
      'comment' => 'The label for the the button at the bottom of the password recovery form.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Change the password',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Schimbă parola',
        ),
      ),
    ),
    'lpcAuthRecoverEmailField' => 
    array (
      'comment' => 'The label for the the user\'s e-mail address field, shown in the password recovery form.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'E-mail address',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Adresa e-mail',
        ),
      ),
    ),
    'lpcAuthRecoverSubject' => 
    array (
      'comment' => 'The subject of the recover password message. Takes one parameter: the name of the project (the value of constant LPC_full_project_name).

It is recommended that you do not overwrite this translation just in order to customize it -- instead, you should redefine your LPC_User\'s descendant variable token_recover_subject to point to another translation key, specific to your project.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Password recovery for {0}',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Recuperare parola {0}',
        ),
      ),
    ),
    'lpcAuthRecoverTitle' => 
    array (
      'comment' => 'The title of the password recovery page',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Password recovery',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Recuperare parolă',
        ),
      ),
    ),
    'lpcAuthResetFormInfo' => 
    array (
      'comment' => 'The introductory message shown above the form used for changing the password (the form where the actual password is filled in). Takes one parameter, the label of the reset button.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => '<p>Please fill in the form below in order to change your password.</p>
<p>If you haven\'t requested the password recovery, please click on the &quot;<i>{0}</i>&quot; button at the end of the form (in this case you don\'t have to fill in the form).</p>',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => '<p>Vă rugăm completaţi formularul de dedesubt pentru a vă schimba parola.</p>
<p>Dacă nu aţi solicitat schimbarea parolei, vă rugăm apăsaţi pe butonul &quot;<i>{0}</i>&quot; de la sfârşitul formularului (în acest caz nu este necesar să completaţi formularul).</p>',
        ),
      ),
    ),
    'lpcAuthResetPasswordButton' => 
    array (
      'comment' => 'The button shown at the bottom of the password reset form (the one where the actual password is being entered).',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Change the password',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Schimbă parola',
        ),
      ),
    ),
    'lpcAuthResetPasswordTitle' => 
    array (
      'comment' => 'The title of the page showing the form for inputting the new password.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Password reset',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Schimbare parolă',
        ),
      ),
    ),
    'lpcAuthTitle' => 
    array (
      'comment' => 'The title of the authentication page',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Authentication',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Autentificare',
        ),
      ),
    ),
    'lpcAuthValidCondAlpha' => 
    array (
      'comment' => 'The password validation condition for alphabetic characters.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'The password must contain at least one letter.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Parola trebuie să conţină cel puţin o literă.',
        ),
      ),
    ),
    'lpcAuthValidConditionsTitle' => 
    array (
      'comment' => 'The title of the validation conditions list shown in the password reset form.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Password validation conditions',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Condiţii de validare a parolei',
        ),
      ),
    ),
    'lpcAuthValidCondMinLength' => 
    array (
      'comment' => 'The password validation condition for minimum password length. Takes one parameter, the minimum length.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'The password must be at least {0} characters long.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Parola trebuie să conţină cel puţin {0} caractere.',
        ),
      ),
    ),
    'lpcAuthValidCondNumeric' => 
    array (
      'comment' => 'The password validation condition for numeric characters.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'The password must contain at least one number.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Parola trebuie să conţină cel puţin o cifră.',
        ),
      ),
    ),
    'lpcFilterIcon' => 
    array (
      'comment' => 'The alternate text for the "filter" icon used in the LPC_HTML_list_filter class.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Filter',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Filtrează',
        ),
      ),
    ),
    'lpcListFilterBooleanHelp' => 
    array (
      'comment' => 'The help message shown for boolean filters.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Only records with values mathing your selection will be displayed. If you select both fields, all records will be shown (same if you deselect both fields).',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Vor fi afişate numai înregistrările care conţin în acest câmp valoarea pe care o selectaţi. Dacă selectaţi ambele câmpuri vor fi afişate toate înregistrările (la fel şi dacă debifaţi ambele câmpuri).',
        ),
      ),
    ),
    'lpcListFilterStringHelp' => 
    array (
      'comment' => 'The help message for the plain string filter in lists. Do NOT use HTML -- it will be shown as such.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'The list will be filtered to show only the records that contain in this field the value you enter here.

The value you specify will be used as such.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Vor fi afişate numai înregistrările care conţin în acest câmp valoarea pe care o introduceţi.

Valoarea pe care o introduceţi va fi folosită ca atare.',
        ),
      ),
    ),
    'lpcListPageLabel' => 
    array (
      'comment' => 'The "Page" label at the bottom of lists.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Page:',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Pagina:',
        ),
      ),
    ),
    'lpcRemoveFilterIcon' => 
    array (
      'comment' => 'The alternate text for the "remove filter" icon used in the LPC_HTML_list_filter class.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Remove filter',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Anulează filtrul',
        ),
      ),
    ),
    'rightsTestCache' => 
    array (
      'comment' => 'The title of the rights test cache page.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Test cached rights',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Testează drepturile din cache',
        ),
      ),
    ),
    'rightsTestSubmit' => 
    array (
      'comment' => 'The rights test submit button label',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Select the user',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Selectează utilizatorul',
        ),
      ),
    ),
    'rightsTestUserID' => 
    array (
      'comment' => 'The label explaining the user ID field in the cached rights test page',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'User ID to investigate:',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'ID-ul utilizatorului pe care doriţi să-l investigaţi:',
        ),
      ),
    ),
    'rightTestGlobalExpDate' => 
    array (
      'comment' => 'Label for the global cache expiration date',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Expiration date for the global cache',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Data de expirare a cache-ului global',
        ),
      ),
    ),
    'rightTestProjectExpDate' => 
    array (
      'comment' => 'Label for project cache expiration date',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Project cache expiration date',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Data de expirare a cache-ului proiectului curent',
        ),
      ),
    ),
    'rightTestUserDate' => 
    array (
      'comment' => 'Label for user cache date',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'User cache date',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Data cache-ului utilizatorului',
        ),
      ),
    ),
    'rightTestUserExpDate' => 
    array (
      'comment' => 'User cache expiration date',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'User cache expiration date',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Data de expirare a cache-ului utilizatorului',
        ),
      ),
    ),
    'rightTestUserPermissions' => 
    array (
      'comment' => 'Permissions list',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'User\'s permissions (from cache)',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Lista permisiunilor utilizatorului (din cache)',
        ),
      ),
    ),
    'scaffoldingActionHeader' => 
    array (
      'comment' => 'The label shown in the header of the scaffolding object list table, on top of the column containing the actions (edit, delete).',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Actions',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Acţiuni',
        ),
      ),
    ),
    'scaffoldingAddThis' => 
    array (
      'comment' => 'The label shown when picking many to many dependencies, prompting the user to add this object to the dependency list.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Add this object',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Adăugați acest obiect',
        ),
      ),
    ),
    'scaffoldingBackToList' => 
    array (
      'comment' => 'The label shown at the top of the object edit page when the "rt" variable is present.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Return to the list',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Înapoi la listă',
        ),
      ),
    ),
    'scaffoldingBackToParent' => 
    array (
      'comment' => 'The label of the link back to the parent object when adding dependencies. Takes two parameters, the class of the parent and its id.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => '&uarr; Back to {0}#{1} &uarr;',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => '&uarr; Înapoi la {0}#{1} &uarr;',
        ),
      ),
    ),
    'scaffoldingBooleanNo' => 
    array (
      'comment' => 'The label for boolean "No" in the scaffolding edit interface.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'No',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Nu',
        ),
      ),
    ),
    'scaffoldingBooleanYes' => 
    array (
      'comment' => 'The label for boolean "Yes" in the scaffolding edit interface.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Yes',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Da',
        ),
      ),
    ),
    'scaffoldingButtonCreate' => 
    array (
      'comment' => 'The label of the "Create" button at the bottom of the scaffolding form.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Create',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Creează',
        ),
      ),
    ),
    'scaffoldingButtonEdit' => 
    array (
      'comment' => 'The label of the "Edit" button at the bottom of the scaffolding form.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Edit',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Modifică',
        ),
      ),
    ),
    'scaffoldingButtonEditPlus' => 
    array (
      'comment' => 'The label for the "submit and attach a new one" button at the bottom of the scaffolding edit form, when attaching a new object.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Submit and attach new',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Trimite şi ataşează din nou',
        ),
      ),
    ),
    'scaffoldingCancelPick' => 
    array (
      'comment' => 'The label of the link shown in the scaffolding object picking form.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Cancel picking object',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Renunţă la alegerea obiectului',
        ),
      ),
    ),
    'scaffoldingColumnVisibilityExplain' => 
    array (
      'comment' => 'Explanation for the column visibility form',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Use the list below to indicate which columns you want visible for this object. You can always adjust this list. This is your personal preference; making changes here does not affect other users.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Folosiţi lista de dedesubt pentru a indica ce coloane doriţi să afişaţi pentru acest obiect. Puteţi modifica această listă oricând. Aceasta este o preferinţă personală de-a dumneavoastră; modificările pe care le faceţi aici nu vor afecta alţi utilizatori.',
        ),
      ),
    ),
    'scaffoldingColumnVisibilityLink' => 
    array (
      'comment' => 'The label of the link to the scaffolding visibility column interface',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Show or hide columns',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Afişează sau ascunde coloane',
        ),
      ),
    ),
    'scaffoldingColumnVisibilityTitle' => 
    array (
      'comment' => 'The title for the column visibility page. Takes one parameter, the class name.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Column visibility for {0}',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Vizibilitatea coloanelor pentru {0}',
        ),
      ),
    ),
    'scaffoldingCreateDependency' => 
    array (
      'comment' => 'The label of the link for attaching one dependency in the scaffolding list. Takes one parameter, the name of the dependency.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Attach {0}',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Ataşează {0}',
        ),
      ),
    ),
    'scaffoldingCreateObject' => 
    array (
      'comment' => 'Label for "Create new object" link in scaffolding lists. It takes one parameter -- the name of the class.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Create new {0} object',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Creează un nou obiect {0}',
        ),
      ),
    ),
    'scaffoldingDeleteAction' => 
    array (
      'comment' => 'The label associated with object deletion in the scaffolding object list.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Delete',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Şterge',
        ),
      ),
    ),
    'scaffoldingDeleteConfirm' => 
    array (
      'comment' => 'The confirmation message shown before deleting objects in LPC\'s scaffolding section.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Are you sure you want to delete this object?',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Sigur doriţi să ştergeţi acest obiect?',
        ),
      ),
    ),
    'scaffoldingDeleteFile' => 
    array (
      'comment' => 'The label of the "delete file" checkbox shown in the scaffolding interface.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Delete this file',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Şterge acest fişier',
        ),
      ),
    ),
    'scaffoldingDownloadFile' => 
    array (
      'comment' => 'The label of the link to a file download in the scaffolding object list.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Download',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Descarcă',
        ),
      ),
    ),
    'scaffoldingEditAction' => 
    array (
      'comment' => 'The label associated with object editing in the scaffolding object list.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Edit',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Modifică',
        ),
      ),
    ),
    'scaffoldingEditLink' => 
    array (
      'comment' => 'The label of the link towards the link object while editing an object in scaffolding. Takes two parameters, the class name of the link and the object ID.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Edit {0} #{1}',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Modifică {0} #{1}',
        ),
      ),
    ),
    'scaffoldingErrorNeedClass' => 
    array (
      'comment' => 'The error message shown in scaffolding pages when a class is needed and it\'s not provided.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Incomplete parameters! (missing class name)',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Parametri incompleţi! (lipseşte numele clasei)',
        ),
      ),
    ),
    'scaffoldingFileDesc' => 
    array (
      'comment' => 'The description shown for LPC files.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'LPC file',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Fişier LPC',
        ),
      ),
    ),
    'scaffoldingLocalizedSection' => 
    array (
      'comment' => 'The title of the localized section of the scaffolding edit table. Feel free to hardcode the language name you\'re translating to, since that\'s ',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Localized section in English (US)',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Secţiune localizată în română',
        ),
      ),
    ),
    'scaffoldingMessageNoObjectsInClass' => 
    array (
      'comment' => 'The label shown in the scaffolding object list when there are no objects in the database.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'This table is empty.',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Această listă este goală.',
        ),
      ),
    ),
    'scaffoldingMsgAvailableClasses' => 
    array (
      'comment' => 'The label shown at the top of the scaffolding class list.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Please pick one of the object types below:',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Vă rugăm alegeţi unul dintre tipurile de obiecte de mai jos:',
        ),
      ),
    ),
    'scaffoldingPickThis' => 
    array (
      'comment' => 'The label of the scaffolding link in the object picking sub-table associated with picking the current object (for linking to in the object being edited).',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Select object',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Selectaţi acest obiect',
        ),
      ),
    ),
    'scaffoldingRemoveThis' => 
    array (
      'comment' => 'The label shown when picking many to many dependencies, prompting the user to remove this object from the dependency list.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Remove this object',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Eliminați acest obiect',
        ),
      ),
    ),
    'scaffoldingSaveError' => 
    array (
      'comment' => 'The message shown when there\'s an exception thrown while trying to save an object in scaffolding. Takes one parameter, the exception message.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'There was a fatal error saving your changes: «{0}»',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'A apărut o eroare fatală la salvarea modificărilor dumneavoastră: «{0}»',
        ),
      ),
    ),
    'scaffoldingSelectLang' => 
    array (
      'comment' => 'The label shown at the top of the scaffolding edit page, prompting the user to change their language (for i18n objects).',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Change the language:',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Schimbă limba:',
        ),
      ),
    ),
    'scaffoldingSetDateNow' => 
    array (
      'comment' => 'The label shown on the button which sets the date to the current time.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => '&larr; now',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => '&larr; acum',
        ),
      ),
    ),
    'scaffoldingSwitchClass' => 
    array (
      'comment' => 'The label shown on scaffolding pages for the link leading to the class list.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Choose a different object type',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Alegeţi un alt tip de obiect',
        ),
      ),
    ),
    'scaffoldingSwitchObject' => 
    array (
      'comment' => 'The label of the scaffolding link to the object list.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'Choose a different entry',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'Alegeţi o altă înregistrare',
        ),
      ),
    ),
    'scaffoldingTitle' => 
    array (
      'comment' => 'Generic page title for LPC scaffolding pages.',
      'system' => '1',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '1',
          'translation' => 'LPC Scaffolding',
        ),
        1 => 
        array (
          'language' => '2',
          'translation' => 'LPC: eşafodaj',
        ),
      ),
    ),
  ),
);