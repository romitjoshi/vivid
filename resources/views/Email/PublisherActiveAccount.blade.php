
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">
</head>
<style>
  p{font-family:Arial, sans-serif, 'Open Sans';font-size:14px}
</style>
<body style="margin:0;padding:0;">
<table style="font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
          <td align="center" style="--bg-opacity: 1; background-color: #eceff1; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif;">
            <table class="sm-w-full" style="font-family: 'Montserrat',Arial,sans-serif; width: 600px;" width="600" cellpadding="0" cellspacing="0" role="presentation">
              <tr>
              <td class="sm-py-32 sm-px-24" style="font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; padding: 25px; text-align: center;" align="center">
                  <a href="{{ env('APP_URL_SERVE') }}">
                      <img src="{{ asset('front/images/logo-email.png') }}" width="155" alt="Vivid" style="border: 0; max-width: 100%; line-height: 100%; vertical-align: middle;">
                    </a>
                </td>
              </tr>
              <tr>
                <td align="center" class="sm-px-24" style="font-family: 'Montserrat',Arial,sans-serif;">
                  <table style="font-family: 'Montserrat',Arial,sans-serif; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                      <td class="sm-px-24" style="--bg-opacity: 1; background-color: #ffffff; border-radius: 4px; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; font-size: 14px; line-height: 24px; padding: 48px; text-align: left; --text-opacity: 1; color: #626262;" align="left">
                        <p style="font-weight: 600; font-size: 18px; margin-bottom: 0; color:#000">Hi,   {{ $mailData['user'] ?? ""}}
                        </p>

                        <p style="margin: 20px 0; color:#000">
                        After further review of your  deactivation, we have determined that your account is not in violation of our terms and have reinstated your access to the platform. All of your content will be visible for other users to read and enjoy. Please click the button below to login with your publisher account to continue to create comics for your audience.
                         </p>

                        <p style="margin-top:60px; color:#000">
                            <a href="{{ $mailData['link'] }}" style="display: block; font-weight: 600; font-size: 14px; line-height: 100%; padding: 16px 24px; --text-opacity: 1; color: #ffffff; color: rgba(255, 255, 255, var(--text-opacity)); text-decoration: none;    width: 30%;background-color: #0d6efd;color: #fff;" >Login &rarr;</a>
                        </p>



                        <p style="margin: 0 0 16px; color:#000">Thanks, <br>The Vivid Team</p>
                      </td>
                    </tr>
                    <tr>
                      <td style="font-family: 'Montserrat',Arial,sans-serif; height: 20px;" height="20"></td>
                    </tr>

                    <tr>
                      <td style="font-family: 'Montserrat',Arial,sans-serif; height: 16px;" height="16"></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </body>
      </html>