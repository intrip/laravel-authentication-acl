<?php  namespace LaravelAcl\Authentication\Validators;
/**
 * Class UserSignupEmailValidator
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Support\Facades\Request;
use LaravelAcl\Authentication\Exceptions\UserNotFoundException;
use LaravelAcl\Library\Validators\AbstractValidator;
use App, Session, Config;

class UserSignupEmailValidator extends AbstractValidator
{
    public function validateEmailUnique($attribute, $value, $parameters)
    {
        $repository = App::make('user_repository');
        try
        {
            $user = $repository->findByLogin($value);
        }
        catch(UserNotFoundException $e)
        {
            return true;
        }


        if($user->activated)
        {
            return false;
        }

        // if email confirmation is disabled we dont send email again
        if(! Config::get('acl_base.email_confirmation') ) return false;

        // send email

        $this->resendConfirmationEmail();
        // set session message
        Session::flash('message', "We sent you again the mail confirmation. Please check your inbox.");
        return false;
    }

    /**
     */
    protected function resendConfirmationEmail()
    {
        $data = Request::all();
        $data['password'] = 'Cannot decipher password, please use password recovery after if it\'s needed.';

        App::make('register_service')->sendRegistrationMailToClient($data);
    }
} 