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