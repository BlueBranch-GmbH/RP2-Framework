<?php

namespace rpf\extension\module;
use rpf\extension\extensionModule;
use rpf\system\module\exception;

/**
 * Class mailExport
 *
 * @author Andreas Doebeling <ad@1601.com>
 * @copyright 1601.production siegler&thuemmler ohg
 * @link https://github.com/ADoebeling/RP2-Framework
 * @link https://xing-ad.1601.com
 * @link https://www.1601.com
 */
class mailExport extends extensionModule
{
    /**
     * Return all active orders with existing e-mail-adresses
     *
     * @return array [ $ordnr[ oeid, cus_company, cus_first_name, cus_last_name, mail_addresses ] ]
     */
    public function getOrderList()
    {
        $orders = $this->getApi()->getOrderReadEntry()->addActiveDispositions()->getArray();
        $mailCountsByOeid = $this->getMailCountsByOeid();

        $return = [];
        foreach ($orders as $row)
        {
            $oeid = $row['oeid'];
            if (!empty($row['has_active_tariff']) && !empty($mailCountsByOeid[$oeid]))
            {
                $return[$row['ordnr']] =
                    [
                        'oeid' => $oeid,
                        'cus_company' => isset($row['cus_company']) ? $row['cus_company'] : '',
                        'cus_first_name' => isset($row['cus_first_name']) ? $row['cus_first_name'] : '',
                        'cus_last_name' => isset($row['cus_last_name']) ? $row['cus_last_name'] : '',
                        'mail_addresses' => $mailCountsByOeid[$oeid],
                    ];
            }
        }
        return $return;
    }

    /**
     * Returns all e-mail-addresses for the given order-ids (oeids)
     *
     * @param array $oeids
     * @return array [ $oeid => [ $email, ... ] ]
     * @throws \Exception
     */
    public function getEmails(array $oeids)
    {
        if (empty($oeids))
        {
            throw new \Exception("Array of order-ids expected", 400);
        }

        $return = [];
        foreach ($oeids as $oeid)
        {
            $accounts = $this->getApi()->getEmailReadAccount()->setOeid($oeid)->getArray(true, 'email');
            if (empty($accounts))
            {
                throw new \Exception("Invalid orderId: $oeid");
            }
            $return[$oeid] = array_keys($accounts);
        }
        return $return;
    }

    /**
     * Returns a description of the current e-mail-config for a/some given orders (oeids)
     *
     * @param int|array $oeids
     * @return array [ $oeid => [ $email => [...] ] ]
     */
    public function getConfigDesc($oeids)
    {
        $oeids = is_int($oeids) ? [$oeids] : $oeids;

        $return = [];
        foreach ($oeids as $oeid)
        {
            $return[$oeid] = $this->getApi()->getEmailReadAccount()->setOeid($oeid)->addUsed()->getArray(true, 'email');
        }
        return $return;
    }

    /**
     * @param array $emails
     * @throws exception
     * @todo Implement: no description of the expected output exists yet.
     */
    public function getEmailsConfigDesc(array $emails)
    {
        throw new exception(__METHOD__.' is not implemented yet', 501);
    }

    /**
     * Counts all existing e-mail-addresses per order (oeid)
     *
     * @return array [ $oeid => $count ]
     */
    protected function getMailCountsByOeid()
    {
        $accounts = $this->getApi()->getEmailReadAccount()->getArray();

        $counts = [];
        foreach ($accounts as $account)
        {
            if (isset($account['oeid']))
            {
                $counts[$account['oeid']] = isset($counts[$account['oeid']]) ? $counts[$account['oeid']] + 1 : 1;
            }
        }
        return $counts;
    }
}
