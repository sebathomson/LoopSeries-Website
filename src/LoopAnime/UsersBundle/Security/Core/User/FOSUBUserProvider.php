<?php
namespace loopanime\UsersBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GoogleResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use LoopAnime\UsersBundle\Entity\Users;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));

        /** @var GoogleResourceOwner $resourceOwner */
        $resourceOwner = $response->getResourceOwner();
        $responseData = $response->getResponse();

        $createTime = new \DateTime("now");
        $country = "UK";
        $birthday = new \DateTime("now");
        if(isset($responseData['birthday']))
            $birthday = new \DateTime($responseData['birthday']);

        switch($resourceOwner->getName()) {
            case "google":
                $avatar = $response->getProfilePicture();
                if(isset($responseData['locale']))
                    $country = explode("-",$responseData['locale'])[1];
                break;
            case "facebook":
                $avatar = str_replace("p50x50","p200x200",$response->getProfilePicture());
                if(isset($responseData['locale']))
                    $country = explode("_",$responseData['locale'])[1];
                break;
            default:
                throw new \Exception("This recourse owner is not declared, therefore i do not know what to populate");
        }

        //when the user is registrating
        if (null === $user) {
            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';
            // create new user here
            /** @var Users $user */
            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());
            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setUsername($username);
            $user->setEmail($response->getEmail());
            $user->setPassword(sha1(time()));
            $user->setAvatar($avatar);
            $user->setBirthdate($birthday);
            $user->setCountry($country);
            $user->setCreateTime($createTime);
            $user->setLang("ENG");
            $user->setNewsletter(1);
            $user->setStatus(1);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
            return $user;
        }

        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        //update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }

}