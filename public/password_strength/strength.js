function passwordStrengthCheck(password1, password2, passwordsInfo)
{
  //Must contain 5 characters or more
  var WeakPass = /(?=.{6,}).*/;
  //Must contain lower case letters and at least one digit.
  var MediumPass = /^(?=\S*?[a-z])(?=\S*?[0-9])\S{5,}$/;
  //Must contain at least one upper case letter, one lower case letter and one digit.
  var StrongPass = /^(?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])\S{5,}$/;
  //Must contain at least one upper case letter, one lower case letter and one digit.
  var VryStrongPass = /^(?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])(?=\S*?[^\w\*])\S{5,}$/;

  $(password1).on('keyup', function(e) {
    if(VryStrongPass.test(password1.val()))
    {
      passwordsInfo.removeClass().addClass('vrystrongpass').html("Very Strong! (Please don't forget your pass now!)");
    }
    else if(StrongPass.test(password1.val()))
    {
      passwordsInfo.removeClass().addClass('strongpass').html("Strong! (Enter special chars to make even stronger");
    }
    else if(MediumPass.test(password1.val()))
    {
      passwordsInfo.removeClass().addClass('goodpass').html("Good! (Enter uppercase letter to make strong)");
    }
    else if(WeakPass.test(password1.val()))
    {
      passwordsInfo.removeClass().addClass('stillweakpass').html("Still Weak! (Enter digits to make good password)");
    }
    else
    {
      passwordsInfo.removeClass().addClass('weakpass').html("Very Weak! (Must be 6 or more chars)");
    }
  });

  $(password2).on('keyup', function(e) {

    if(password1.val() !== password2.val())
    {
      passwordsInfo.removeClass().addClass('weakpass').html("Passwords do not match!");
    }else{
      passwordsInfo.removeClass().addClass('goodpass').html("Passwords match!");
    }

  });
}
