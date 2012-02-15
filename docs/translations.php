<?php

// Automated export on Wed, 15 Feb 2012 21:13:38 +0000

return array (
  'languages' => 
  array (
    0 => 
    array (
      'id' => '1',
      'name' => 'English (US)',
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
          'translation' => 'Eroare a cheiei de sesiun! Fie aţi încercat să continuaţi o sesiune mai veche sau, dacă veniţi aici de la o sursă externă (website, e-mail), cineva încearcă să se folosească în mod abuziv de drepturile dumneavoastră.',
        ),
      ),
    ),
    'genericNoName' => 
    array (
      'comment' => 'Fără nume',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => '(nume indisponibil)',
        ),
      ),
    ),
    'lpcAuthCancelResetButton' => 
    array (
      'comment' => 'The label on the button used for canceling the password reset request.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Anulează cererea de recuperare a parolei',
        ),
      ),
    ),
    'lpcAuthConfirmRecoverEmail' => 
    array (
      'comment' => 'The confirmation message shown when the password recovery e-mail is sent (or not sent, if the e-mail wasn\'t found in the database). Takes one parameter, the e-mail address supplied by the user.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Dacă există vreun utilizator înregistrat care foloseşte adresa e-mail pe care aţi indicat-o (<tt>{0}</tt>) atunci mesajul a fost trimis cu succes.',
        ),
      ),
    ),
    'lpcAuthCreatePasswordFormInfo' => 
    array (
      'comment' => 'The introductory message shown on the password creation form.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Felicitări, aproape aţi terminat procesul de înregistrare! Vă rugăm introduceţi parola pe care doriţi să o folosiţi în formularul de dedesubt.',
        ),
      ),
    ),
    'lpcAuthCreatePasswordLabel' => 
    array (
      'comment' => 'The label of the button at the end of the password creation form.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Creare cont',
        ),
      ),
    ),
    'lpcAuthCreatePasswordTitle' => 
    array (
      'comment' => 'The title of the password creation page.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Creare cont',
        ),
      ),
    ),
    'lpcAuthDoneCancel' => 
    array (
      'comment' => 'The confirmation message shown when the password reset request is successfully cancelled.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Cererea de recuperare a parolei a fost anulată cu succes.',
        ),
      ),
    ),
    'lpcAuthDoneResetConfirm' => 
    array (
      'comment' => 'The confirmation message shown when the password reset succeeds. Takes one parameter, the username for the user who changed their password. Be advised the username may actually be their e-mail address; also, they have already been logged in automatically at this point.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Parola a fost schimbată cu succes. Aţi fost deja autentificat ca {0}, iar în viitor vă puteţi autentifica cu parola pe care tocmai aţi introdus-o.',
        ),
      ),
    ),
    'lpcAuthEmail' => 
    array (
      'comment' => 'The label for the authentication field "e-mail"',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Adresa e-mail',
        ),
      ),
    ),
    'lpcAuthErrAlreadyLoggedOn' => 
    array (
      'comment' => 'The error shown to authenticated users who want to recover a password.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Deja sunteţi autentificat!',
        ),
      ),
    ),
    'lpcAuthErrConfirm' => 
    array (
      'comment' => 'The authentication-related error message shown when the password confirmation differs from the password field',
      'translations' => 
      array (
        0 => 
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
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Parola este prea scurtă! Vă rugăm asiguraţi-vă că parola pe care o alegeţi are cel puţin {0} caractere.',
        ),
      ),
    ),
    'lpcAuthErrNeedAlpha' => 
    array (
      'comment' => 'The authentication-related error message shown when the password doesn\'t contain any letters.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Vă rugăm asiguraţi-vă că parola pe care o alegeţi conţine cel puţin o literă!',
        ),
      ),
    ),
    'lpcAuthErrNeedNumber' => 
    array (
      'comment' => 'The authentication-related error message shown when the password doesn\'t contain any numbers.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Vă rugăm asiguraţi-vă că parola pe care o alegeţi conţine cel puţin o cifră!',
        ),
      ),
    ),
    'lpcAuthInvalidToken' => 
    array (
      'comment' => 'The error message shown when a user tries to recover a password with an invalid token/e-mail combination.',
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
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Doamnă/domnule {0},

Bun venit la {2}! Pentru a finaliza procesul de înregistrare, vă rugăm apăsaţi pe legătura de dedesubt, sau copiaţi-o în bara de adrese a navigatorului dumneavoastră web:

{3}

Vă mulţumim,
Echipa {1}',
        ),
      ),
    ),
    'lpcAuthInviteSubject' => 
    array (
      'comment' => 'The invitation e-mail\'s subject. Takes one parameter: the name of the project (the value of constant LPC_full_project_name).

It is recommended that you do not overwrite this translation just in order to customize it -- instead, you should redefine your LPC_User\'s descendant variable token_invite_subject to point to another translation key, specific to your project.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Cont nou {0}',
        ),
      ),
    ),
    'lpcAuthLogIn' => 
    array (
      'comment' => 'The label for the authentication "Log in" button',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Autentificare',
        ),
      ),
    ),
    'lpcAuthPassword' => 
    array (
      'comment' => 'The label for the authentication field "password"',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Parola',
        ),
      ),
    ),
    'lpcAuthPasswordConfirm' => 
    array (
      'comment' => 'The label for the password confirmation field in the password reset form.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Confirmaţi parola',
        ),
      ),
    ),
    'lpcAuthPasswordConfirmExplain' => 
    array (
      'comment' => 'The explanations for the password confirmation field shown in the password reset form.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Introduceţi din nou aceeaşi parolă pentru confirmare',
        ),
      ),
    ),
    'lpcAuthPasswordFieldExplain' => 
    array (
      'comment' => 'The explanation for the first password field in the password reset form.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Introduceţi noua parolă pe care doriţi să o utilizaţi',
        ),
      ),
    ),
    'lpcAuthRecover' => 
    array (
      'comment' => 'The label for the authentication link "recover password"',
      'translations' => 
      array (
        0 => 
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
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Doamnă/domnule {0},

Cineva a solicitat recuperarea parolei pentru contul asociat adresei dumneavoastră e-mail ({4}) pe site-ul clinicbouquet.ro. Indiferent dacă aţi solicitat recuperarea parolei sau nu, vă rugăm apăsaţi pe legătura de mai jos (dacă nu aţi solicitat recuperarea parolei veţi avea ocazia să indicaţi acest lucru).

{3}

Vă mulţumim,
Echipa {1}',
        ),
      ),
    ),
    'lpcAuthRecoverButton' => 
    array (
      'comment' => 'The label for the the button at the bottom of the password recovery form.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Schimbă parola',
        ),
      ),
    ),
    'lpcAuthRecoverEmailField' => 
    array (
      'comment' => 'The label for the the user\'s e-mail address field, shown in the password recovery form.',
      'translations' => 
      array (
        0 => 
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
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Recuperare parola {0}',
        ),
      ),
    ),
    'lpcAuthRecoverTitle' => 
    array (
      'comment' => 'The title of the password recovery page',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Recuperare parolă',
        ),
      ),
    ),
    'lpcAuthResetFormInfo' => 
    array (
      'comment' => 'The introductory message shown above the form used for changing the password (the form where the actual password is filled in). Takes one parameter, the label of the reset button.',
      'translations' => 
      array (
        0 => 
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
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Schimbă parola',
        ),
      ),
    ),
    'lpcAuthResetPasswordTitle' => 
    array (
      'comment' => 'The title of the page showing the form for inputting the new password.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Schimbare parolă',
        ),
      ),
    ),
    'lpcAuthTitle' => 
    array (
      'comment' => 'The title of the authentication page',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Autentificare',
        ),
      ),
    ),
    'lpcAuthValidCondAlpha' => 
    array (
      'comment' => 'The password validation condition for alphabetic characters.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Parola trebuie să conţină cel puţin o literă.',
        ),
      ),
    ),
    'lpcAuthValidConditionsTitle' => 
    array (
      'comment' => 'The title of the validation conditions list shown in the password reset form.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Condiţii de validare a parolei',
        ),
      ),
    ),
    'lpcAuthValidCondMinLength' => 
    array (
      'comment' => 'The password validation condition for minimum password length. Takes one parameter, the minimum length.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Parola trebuie să conţină cel puţin {0} caractere.',
        ),
      ),
    ),
    'lpcAuthValidCondNumeric' => 
    array (
      'comment' => 'The password validation condition for numeric characters.',
      'translations' => 
      array (
        0 => 
        array (
          'language' => '2',
          'translation' => 'Parola trebuie să conţină cel puţin o cifră.',
        ),
      ),
    ),
    'scaffoldingActionHeader' => 
    array (
      'comment' => 'The label shown in the header of the scaffolding object list table, on top of the column containing the actions (edit, delete).',
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
    'scaffoldingBooleanNo' => 
    array (
      'comment' => 'The label for boolean "No" in the scaffolding edit interface.',
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
    'scaffoldingCreateDependency' => 
    array (
      'comment' => 'The label of the link for attaching one dependency in the scaffolding list. Takes one parameter, the name of the dependency.',
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
    'scaffoldingDownloadFile' => 
    array (
      'comment' => 'The label of the link to a file download in the scaffolding object list.',
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
    'scaffoldingLocalizedSection' => 
    array (
      'comment' => 'The title of the localized section of the scaffolding edit table. Feel free to hardcode the language name you\'re translating to, since that\'s ',
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
    'scaffoldingSwitchClass' => 
    array (
      'comment' => 'The label shown on scaffolding pages for the link leading to the class list.',
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