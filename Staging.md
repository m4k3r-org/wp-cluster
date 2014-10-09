In order to properly access the staging environment, you must take a few steps to do so first. In a nutshell, you'll need to connect to our production environment via VPN, and then forward necessary ports from your local machine through our Docker hosts and into the staging Docker instance via SSH tunneling.

**Remember: The Staging environment is connected to the production database, so be very careful here.**

## Step 1: Modify Your Hosts File

Modify your HOSTS file to add the following domains.

### Ramadi 

Add Ramadi, as it's the machine we'll be connecting through (via SSH).

```
208.52.164.204 ramadi
```

### Drop Network Sites

Add the actual drop network sites, and point them to '127.0.0.1', or you local machine.

```
127.0.0.1 2013.freaksbeatstreats.com
127.0.0.1 2013.monsterblockparty.com
127.0.0.1 2014.dayafter.com
127.0.0.1 bassodyssey.com
127.0.0.1 beachblanketfestival.com
127.0.0.1 ccf.discodonniepresents.com
127.0.0.1 cominghomemusicfestival.com
127.0.0.1 dayafter.com
127.0.0.1 discodonniepresents.com
127.0.0.1 edm.wpcloud.io
127.0.0.1 freaksbeatstreats.com
127.0.0.1 galveston.beachblanketfestival.com
127.0.0.1 gifttampa.com
127.0.0.1 gxgmag.com
127.0.0.1 hardwellmx-com.discodonniepresents.com
127.0.0.1 hififest.com
127.0.0.1 hireheadline.com
127.0.0.1 isladelsolfest.com
127.0.0.1 mexico.lightsallnight.com
127.0.0.1 monsterblockparty.com
127.0.0.1 old.cominghomemusicfestival.com
127.0.0.1 old.dayafter.com
127.0.0.1 old.isladelsolfest.com
127.0.0.1 old.somethingwicked.com
127.0.0.1 smftampa.com
127.0.0.1 somethingwicked.com
127.0.0.1 sugarsociety.com
127.0.0.1 suncitymusicfestival.com
127.0.0.1 umesouthpadre.com
127.0.0.1 wildwood.beachblanketfestival.com
127.0.0.1 winterfantasyrgv.com
```

## Step 2: Connect to the VPN

See the following page for detailed instructions on how to connect to the VPN using OSX: 
[How do I configure the OS X integrated IPSec VPN client?](https://faq.oit.gatech.edu/content/how-do-i-configure-os-x-integrated-ipsec-vpn-client)

### Connection Details

```
IP Address: 208.52.179.2
Group Name: UsabilityDynamics-VPN
Group Password: praYefe6
User Name: *private*
User Password: *private*
```

Once you're connected, verify that you can ping the Ramadi host by using the following command:

```
$ ping ramadi
PING ramadi (208.52.164.204): 56 data bytes
64 bytes from 208.52.164.204: icmp_seq=0 ttl=64 time=30.564 ms
64 bytes from 208.52.164.204: icmp_seq=1 ttl=64 time=32.172 ms
64 bytes from 208.52.164.204: icmp_seq=2 ttl=64 time=30.497 ms
```

## Step 3: Configure SSH

Now, the next step is to configure SSH so that when you connect to Ramadi, your ports on your local machine will forward to the necessary ports on the Docker container running inside Ramadi. To do this on Mac, run the following commands in a terminal. After you do this, you can close the terminal.

```
$ sudo su root
$ nano ~/.ssh/config
```

**Note: On OSX, you have to do this as the root user using 'sudo' due to the fact that normal users are not allowed to run things on low level ports.**

### Contents of the 'config' file

```
Host ramadi
User root
Port 22
IdentityFile *path to your private key*
# Staging
LocalForward 80 172.17.0.17:8080
LocalForward 1134 172.17.0.17:22
LocalForward 8000 172.17.0.17:8000
```

**Why?: The last 3 lines forward ports 80, 1134, and 8000 from your local machine (127.0.0.1) to the remote staging Docker instance.**

## Step 4: SSH and Establish the Tunnel

At this point, all you should need to do is run the terminal, and type the following command to SSH into Ramadi as the root user locally. You don't need to do anything with this terminal, as just establishing the connection creates the tunnel you need to test out all the sites.

```
$sudo ssh ramadi
```

## Step 5: Verify Site

Now, all you need to do is goto any one of the sites mentioned above in the HOSTS file modification, and checking to see that you're looking at the staging content.

## Recommended Tools

* [Gas Mask](http://www.macupdate.com/app/mac/29949/gas-mask) - Allows you to quickly switch between different sets of HOSTS on OSX
* [Server IP](https://chrome.google.com/webstore/detail/server-ip/lllhkijapbmlekoldcoohglpihmcjdgj?hl=en-UShttps://chrome.google.com/webstore/detail/server-ip/lllhkijapbmlekoldcoohglpihmcjdgj?hl=en-US) - Chrome add-on that shows you the IP of the server you're currently looking at